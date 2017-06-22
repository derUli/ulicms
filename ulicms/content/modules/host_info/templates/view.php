<?php
$version = new ulicms_version ();
?>
<p>
	<strong><?php translate("uptime");?></strong><br />
<pre><?php system("uptime");?></pre>
</p>
<p>
	<strong><?php translate("operating_system");?></strong><br />
<pre><?php system("uname -a"); ?></pre>
</p>
<p>
	<strong><?php translate("memory_usage_mb");?></strong><br />
<pre><?php system("free -m"); ?></pre>
</p>
<p>
	<strong><?php translate("disk_usage");?></strong><br />
<pre><?php system("df -h"); ?></pre>
</p>
<p>
	<strong><?php translate("cpu_info");?></strong><br />
<pre><?php system("cat /proc/cpuinfo | grep \"model name\\|processor\""); ?></pre>
</p>
<p>
	<strong><?php translate("php_version");?></strong><br />
<pre><?php Template::escape(phpversion()); ?></pre>
</p>
<p>
	<strong><?php translate("mysql_client_version");?></strong><br />
<pre><?php Template::escape(Database::getClientInfo()); ?></pre>
</p>
<p>
	<strong><?php translate("mysql_server_version");?></strong><br />
<pre><?php Template::escape(Database::getServerVersion()); ?></pre>
</p>

<p>
	<strong><?php translate("ulicms_version");?></strong><br />
<pre><?php Template::escape($version->getInternalVersionAsString()); ?></pre>
</p>
<?php translate("server_vars")?>
<table class="tablesorter">
	<thead>
		<tr>
			<th>
                    <?php translate("name");?>
                </th>
			<th>
                    <?php translate("value");?>
                </th>
		</tr>
	</thead>
	<tbody>            
            <?php foreach ( $_SERVER as $key => $value ) {
				if(is_string($value)){
				?>
            <tr>
			<td><?php Template::escape($key);?></td>
			<td><?php echo nl2br(Template::getEscape($value));?></td>
		</tr>		
                <?php }}?>
        </tbody>
</table>