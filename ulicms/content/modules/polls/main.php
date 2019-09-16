<?php

function polls_render()
{
    $html = "";
    
    $data = CustomData::get();
    
    if (isset($data["poll_id"]) and is_numeric($data["poll_id"])) {
        $id = intval($data["poll_id"]);
        
        $question = new Question($id);
        
        if (is_null($question->getID())) {
            $html = get_translation("poll_not_found");
        } else {
            // FIXME: Prüfen, auf $question->isEnabled()
            // Wenn Poll deaktiviert, dann Meldung, dass Poll nicht mehr aktiv ist
            $already_voted = PollFactory::userHasAlreadyVotedForPoll($id);
            if (! $already_voted) {
                if ($question->isEnabled()) {
                    $html = Template::executeModuleTemplate("polls", "poll");
                } else {
                    $html = Template::executeModuleTemplate("polls", "disabled_poll");
                }
            } else {
                $html = Template::executeModuleTemplate("polls", "graph");
            }
        }
    } else {
        $html = get_translation("poll_id_not_set");
    }
    
    return $html;
}
