<?php

declare(strict_types=1);

namespace UliCMS\Models\Users;

use User;

// provides the same method as in Group model but as collection of all groups
// of a user
class GroupCollection
{
    private $user;

    public function __construct(?User $user = null)
    {
        $this->user = $user ? $user : new User();
    }

    // return allowable tags of all groups of the user as string
    public function getAllowableTags(): string
    {
        $groups = $this->user->getAllGroups();
        $tagString = "";
        foreach ($groups as $group) {
            if ($group->getAllowableTags()) {
                $tagString .= $group->getAllowableTags();
            }
        }

        $tags = [];
        preg_match_all('/<([a-z]+)>/i', $tagString, $tags);


        $tags = count($tags) > 1 ? $tags[1] : [];
        $tags = array_map('strtolower', $tags);
        $tags = array_unique($tags);
        $tags = array_filter($tags);
        sort($tags);

        return $this->joinTags($tags);
    }

    private function joinTags(array $tags): string
    {
        $tags = array_map(
            function ($tag) {
                    return "<{$tag}>";
                },
            $tags
        );

        return implode("", $tags);
    }
}
