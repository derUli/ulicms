<?php

// FIXME: Fehlerbehandlung (Kein Repo vorhanden=)
// FIXME: Sicherheit!
// TODO: Echtzeit Aktualisierung - Per Ajax auf Änderungen am Status pollen
class GitClient extends Controller {

    const MODULE_NAME = "git_client";

    public function settings() {
        ViewBag::set("current_branch", $this->getCurrentBranch());
        ViewBag::set("has_changes", $this->getGitRepository()->hasChanges());
        return Template::executeModuleTemplate(self::MODULE_NAME, "main.php");
    }

    public function pull() {
        $this->getGitRepository()->pull();
        $lastCommitId = $this->getGitRepository()->getLastCommitId();
        $commitData = $this->getGitRepository()->getCommitData($lastCommitId);

        $message = "<strong>" . get_translation("latest_commit_is_now") . "</strong><br/>";
        foreach ($commitData as $key => $value) {
            if ($value !== null) {
                $translatedKey = get_translation($key);
                $message .= UliCMS\HTML\text("$translatedKey: $value\n");
            }
        }

        HTMLResult($message);
    }

    public function getSettingsHeadline() {
        return get_translation("git_client_headline", array("%branch%" => $this->getCurrentBranch()));
    }

    public function getCurrentBranch() {
        return $this->getGitRepository()->getCurrentBranchName();
    }

    public function getGitRepository() {
        return new Cz\Git\GitRepository(ULICMS_ROOT);
    }

}
