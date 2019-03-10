<?php

class GitClient extends Controller {

    const MODULE_NAME = "git_client";

    public function settings() {
        ViewBag::set("current_branch", $this->getCurrentBranch());
        return Template::executeModuleTemplate(self::MODULE_NAME, "main.php");
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
