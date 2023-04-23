<?php

declare(strict_types=1);

namespace App\Models\Users;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use User;

/**
 * This class contains methods to join group settings together
 */
class GroupCollection {
    private $user;

    /**
     * Constructor
     * @param User|null $user
     */
    public function __construct(?User $user = null) {
        $this->user = $user ?: new User();
    }

    /**
     * Join allowed Html Tags of all users together
     * @return string
     */
    public function getAllowableTags(): string {
        $groups = $this->user->getAllGroups();
        $tagString = '';

        // Iterate over all groups and join its allowable tags
        foreach ($groups as $group) {
            if ($group->getAllowableTags()) {
                $tagString .= $group->getAllowableTags();
            }
        }

        $tags = [];
        // extract tag names
        preg_match_all('/<([a-z]+)>/i', $tagString, $tags);


        // If there are matches get it
        $tags = count($tags) > 1 ? $tags[1] : [];

        // Convert to lowercase
        $tags = array_map('strtolower', $tags);

        // Remove duplicates
        $tags = array_unique($tags);

        /// Remove empty
        $tags = array_filter($tags);

        // Sort alphabetically
        sort($tags);

        // Join together
        return $this->joinTags($tags);
    }

    /**
     * Join all tags together to make it usable with strip_tags()
     * @param array $tags
     * @return string
     */
    private function joinTags(array $tags): string {
        $tags = array_map(
            static function($tag) {
                return "<{$tag}>";
            },
            $tags
        );

        return implode('', $tags);
    }
}
