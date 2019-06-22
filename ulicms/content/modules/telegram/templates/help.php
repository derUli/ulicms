<div class="form-group">
    <a href="<?php echo ModuleHelper::buildAdminURL("telegram"); ?>"
       class="btn btn-default btn-back">
        <i class="fa fa-arrow-left"></i> <?php translate("back"); ?></a>
</div>
<h1>Konfiguration des Telegram Bots</h1>
<ol>
    <li>Erstellen Sie einen öffentlichen Telegram Kanal.</li>
    <li>Erstellen Sie mit BotFather einen Telegram Bot gemäß folgender <a href="https://www.christian-luetgens.de/homematic/telegram/botfather/Chat-Bot.htm" target="_blank">Anleitung</a>.</li>
    <li>Fügen Sie den Bot als Admin zu Ihrem Telegram Kanal hinzu.</li>
    <li>Öffnen Sie nun die Einstellungen des Menüs</li>
    <li>Tragen Sie den Bot Token ein, den Sie vom BotFather erhalten haben.</li>
    <li>Tragen Sie den Kanalnamen ein, dies ist der Name in der URL zu Ihrem Telegram-Kanal. Dieser muss mit einem @-Zeichen beginnen. Wenn die URL Ihres Telegram Kanals beispielsweise t.me/meinkanal ist, müssen sie in das Textfeld @meinkanal eintragen.</li>
    <li>Stellen Sie mit den Schaltern ein, welche Inhalte automatisch gepostet werden sollen.</li>
</ol>
