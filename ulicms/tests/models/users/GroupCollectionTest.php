<?php

declare(strict_types=1);

use UliCMS\Models\Users\GroupCollection;

class GetGroupCollectionTest extends \PHPUnit\Framework\TestCase {

    public function testGetAllowedTags() {
        $user = $this->getTestUser();
        $collection = new GroupCollection($user);

        $this->assertEquals(
                "<div><foo><img><p><span><strong><video>",
                $collection->getAllowableTags()
        );
    }

    private function getTestUser(): User {
        $user = new User();

        $group1 = new Group();
        $group1->setAllowableTags("<p><div><strong><span><img>");

        $group2 = new Group();
        $group2->setAllowableTags("<p><img><foo>");

        $group3 = new Group();
        $group3->setAllowableTags("<video><audio");

        $user->setPrimaryGroup($group1);
        $user->setSecondaryGroups([$group2, $group3]);

        return $user;
    }

}
