<?php

// FIXME: Fehlerbehandlung (Kein Repo vorhanden=)
// FIXME: Sicherheit!
// TODO: Echtzeit Aktualisierung - Per Ajax auf Änderungen am Status pollen
class GitClient extends Controller {

    const MODULE_NAME = "git_client";

    public function settings() {
        try {
            ViewBag::set("has_changes", $this->getGitRepository()->hasChanges());
            ViewBag::set("branch", $this->getGitRepository()->getCurrentBranchName());
            $branches = $this->getGitRepository()->getBranches();
            $options = array();
            foreach ($branches as $branch) {
                $options[] = new UliCMS\HTML\ListItem($branch, $branch);
            }
            ViewBag::set("branches", $options);
        } catch (Cz\Git\GitException $e) {
            // this is required because git-repository changes the cwd
            // and when an exception happens it doesn't change back to admin dir
            chdir(Path::resolve("ULICMS_ROOT/admin"));
            return $this->showError($e->getMessage());
        }
        return Template::executeModuleTemplate(self::MODULE_NAME, "main.php");
    }

    private function showError($message) {
        ViewBag::set("error", $message);
        return Template::executeModuleTemplate(self::MODULE_NAME, "error.php");
    }

    public function pull() {
        try {
            $this->getGitRepository()->pull();
            $lastCommitId = $this->getGitRepository()->getLastCommitId();
            $commitData = $this->getGitRepository()->getCommitData($lastCommitId);
        } catch (Cz\Git\GitException $e) {
            // this is required because git-repository changes the cwd
            // and when an exception happens it doesn't change back to admin dir
            chdir(Path::resolve("ULICMS_ROOT/admin"));
            HtmlResult(UliCMS\HTML\text($e->getMessage()));
        }

        $message = "<strong>" . get_translation("latest_commit_is_now") . "</strong><br/>";
        foreach ($commitData as $key => $value) {
            if ($value !== null) {
                $translatedKey = get_translation($key);
                $message .= "<strong>$translatedKey:</strong> " . UliCMS\HTML\text("$value\n");
            }
        }

        HTMLResult($message);
    }

    public function getSettingsHeadline() {
        return get_translation("git_client_headline");
    }

    public function getCurrentBranch() {
        return $this->getGitRepository()->getCurrentBranchName();
    }

    public function getGitRepository() {
        return new Cz\Git\GitRepository(ULICMS_ROOT);
    }

    public function checkForChanges() {
        JSONResult($this->getGitRepository()->hasChanges());
    }

    public function commitAndPush() {
        $message = Request::getVar("message");
        if (!$message) {
            ExceptionResult(get_translation("fill_all_fields"), HTTPStatusCode::UNPROCESSABLE_ENTITY);
        }
        try {
            $this->getGitRepository()->addAllChanges();
            $this->getGitRepository()->commit($message);
            $this->getGitRepository()->push();
        } catch (Cz\Git\GitException $e) {
            chdir(Path::resolve("ULICMS_ROOT/admin"));
            ExceptionResult($e->getMessage());
        }
        Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME));
    }

    public function createBranch() {
        $name = Request::getVar("name");
        if (!$name) {
            ExceptionResult(get_translation("fill_all_fields"), HTTPStatusCode::UNPROCESSABLE_ENTITY);
        }
        try {
            $this->getGitRepository()->createBranch($name, true);
            Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME));
        } catch (Cz\Git\GitException $e) {
            chdir(Path::resolve("ULICMS_ROOT/admin"));
            ExceptionResult($e->getMessage());
        }
    }

    public function checkoutBranch() {
        $name = Request::getVar("name");
        if (!$name) {
            ExceptionResult(get_translation("fill_all_fields"), HTTPStatusCode::UNPROCESSABLE_ENTITY);
        }
        try {
            $this->getGitRepository()->checkout($name);
            Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME));
        } catch (Cz\Git\GitException $e) {
            chdir(Path::resolve("ULICMS_ROOT/admin"));
            ExceptionResult($e->getMessage());
        }
    }

}
