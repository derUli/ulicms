<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();

	if ($acl->hasPermission ( "pages" )) {
		?>
<h2>
<?php

		echo TRANSLATION_PAGES;
		?>
</h2>
<p>
<?php
		echo TRANSLATION_PAGES_INFOTEXT;
		?>
</p>
<p>
	<a href="index.php?action=pages_new"><?php

		echo TRANSLATION_CREATE_PAGE;
		?>
	</a>
</p>

<script type="text/javascript">
function filter_by_language(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_language=" + element.options[index].value)
   }
}

function filter_by_type(element){
	   var index = element.selectedIndex
	   if(element.options[index].value != ""){
	     location.replace("index.php?action=pages&filter_type=" + element.options[index].value)
	   }
	}


function filter_by_menu(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_menu=" + element.options[index].value)
   }
}

function filter_by_active(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_active=" + element.options[index].value)
   }
}

function filter_by_approved(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_approved=" + element.options[index].value)
   }
}

function filter_by_parent(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_parent=" + element.options[index].value)
   }
}

function filter_by_status(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_status=" + element.options[index].value)
   }
}

$(window).load(function(){
   $('#category').on('change', function (e) {
   var valueSelected = $('#category').val();
     location.replace("index.php?action=pages&filter_category=" + valueSelected)

   });

});

</script>
<?php
		if (! isset ( $_SESSION ["filter_title"] )) {
			$_SESSION ["filter_title"] = "";
		}

		if (isset ( $_GET ["filter_title"] )) {
			$_SESSION ["filter_title"] = $_GET ["filter_title"];
		}
		?>
<form method="get" action="index.php">
	<?php translate("title");?>
	<input type="hidden" name="action" value="pages"> <input type="text"
		name="filter_title"
		value="<?php echo htmlspecialchars($_SESSION["filter_title"]);?>">

</form>

<?php

		echo TRANSLATION_FILTER_BY_LANGUAGE;
		?>
<select name="filter_language" onchange="filter_by_language(this)">
	<option value="">
		<?php

		echo TRANSLATION_PLEASE_SELECT;
		?>
		</option>
		<?php
		if (! empty ( $_GET ["filter_language"] ) and in_array ( $_GET ["filter_language"], getAllLanguages () )) {
			$_SESSION ["filter_language"] = $_GET ["filter_language"];
			$_SESSION ["filter_parent"] = null;
		}

		if (! isset ( $_SESSION ["filter_category"] )) {
			$_SESSION ["filter_category"] = 0;
		}

		if (isset ( $_GET ["filter_active"] )) {
			if ($_GET ["filter_active"] === "null")
				$_SESSION ["filter_active"] = null;
			else
				$_SESSION ["filter_active"] = intval ( $_GET ["filter_active"] );
		}

		if (isset ( $_GET ["filter_approved"] )) {
		  if ($_GET ["filter_approved"] === "null")
		    $_SESSION ["filter_approved"] = null;
		  else
		    $_SESSION ["filter_approved"] = intval ( $_GET ["filter_approved"] );
		}

		if (isset ( $_GET ["filter_type"] )) {
			if ($_GET ["filter_type"] == "null") {
				$_SESSION ["filter_type"] = null;
			} else {
				$_SESSION ["filter_type"] = $_GET ["filter_type"];
			}
		}

		if (isset ( $_GET ["filter_menu"] )) {
			if ($_GET ["filter_menu"] == "null")
				$_SESSION ["filter_menu"] = null;
			else
				$_SESSION ["filter_menu"] = $_GET ["filter_menu"];
		}

		if (isset ( $_GET ["filter_parent"] )) {
			if ($_GET ["filter_parent"] == "null")
				$_SESSION ["filter_parent"] = null;
			else
				$_SESSION ["filter_parent"] = $_GET ["filter_parent"];
		}

		if (! isset ( $_SESSION ["filter_parent"] )) {
			$_SESSION ["filter_parent"] = null;
		}

		if (! isset ( $_SESSION ["filter_menu"] )) {
			$_SESSION ["filter_menu"] = null;
		}
		if (! isset ( $_SESSION ["filter_type"] )) {
			$_SESSION ["filter_type"] = null;
		}

		if (! isset ( $_SESSION ["filter_active"] )) {
			$_SESSION ["filter_active"] = null;
		}

	 if (! isset ( $_SESSION ["filter_approved"] )) {
					$_SESSION ["filter_approved"] = null;
				}

		if (isset ( $_GET ["filter_category"] )) {
			$_SESSION ["filter_category"] = intval ( $_GET ["filter_category"] );
		}

		if (! empty ( $_GET ["filter_status"] ) and in_array ( $_GET ["filter_status"], array (
				"Standard",
				"standard",
				"trash"
		) )) {
			$_SESSION ["filter_status"] = $_GET ["filter_status"];
		}

		$languages = getAllLanguages ();
		for($j = 0; $j < count ( $languages ); $j ++) {
			if ($languages [$j] == $_SESSION ["filter_language"]) {
				echo "<option value='" . $languages [$j] . "' selected>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
			} else {
				echo "<option value='" . $languages [$j] . "'>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
			}
		}

		$menus = getAllMenus ( true );

		array_unshift ( $menus, "null" );

		$sql = "select a.id as id, a.title as title from " . tbname ( "content" ) . " a inner join " . tbname ( "content" ) . " b on a.id = b.parent ";

		if (in_array ( $_SESSION ["filter_language"], getAllLanguages () )) {
			$sql .= "where b.language='" . $_SESSION ["filter_language"] . "' ";
		}

		$sql .= " group by a.title ";
		$sql .= " order by a.title";
		$parents = db_query ( $sql );
		?>

	</select>

<?php translate("type")?>
<?php $types = get_available_post_types();?>
<select name="filter_type" onchange="filter_by_type(this);">
	<option value="null"
		<?php

		if ("null" == $_SESSION ["filter_type"])
			echo "selected";
		?>>
			[<?php
		translate ( "every" )?>]
		</option>
		<?php

		foreach ( $types as $type ) {
			if ($type == $_SESSION ["filter_type"]) {
				echo '<option value="' . $type . '" selected>' . get_translation ( $type ) . "</option>";
			} else {
				echo '<option value="' . $type . '">' . get_translation ( $type ) . "</option>";
			}
		}
		?>
	</select>


<?php
		translate ( "status" )?>
<select name="filter_status" onchange="filter_by_status(this)">
	<option value="Standard"
		<?php
		if ($_SESSION ["filter_status"] == "standard") {
			echo " selected";
		}
		?>>
		<?php

		echo TRANSLATION_STANDARD;
		?>
		</option>
	<option value="trash"
		<?php
		if ($_SESSION ["filter_status"] == "trash") {
			echo " selected";
		}
		?>>
		<?php

		echo TRANSLATION_RECYCLE_BIN;
		?>
		</option>
</select>
<?php

		echo TRANSLATION_CATEGORY;
		?>
	<?php
		echo categories::getHTMLSelect ( $_SESSION ["filter_category"], true );
		?>
	<?php

		echo TRANSLATION_MENU;
		?>
<select name="filter_menu" onchange="filter_by_menu(this);">

	<?php
		foreach ( $menus as $menu ) {
			if ($menu == "null")
				$name = "[" . TRANSLATION_EVERY . "]";
			else
				$name = $menu;

			if ($menu == $_SESSION ["filter_menu"])
				echo '<option value="' . $menu . '" selected>' . get_translation ( $name ) . "</option>";
			else
				echo '<option value="' . $menu . '">' . get_translation ( $name ) . "</option>";
		}

		?>
	</select>
<?php

		echo TRANSLATION_PARENT;
		?>
<select name="filter_parent" onchange="filter_by_parent(this);">
	<option value="null"
		<?php

		if ("null" == $_SESSION ["filter_parent"])
			echo "selected";
		?>>
			[<?php

		echo TRANSLATION_EVERY;
		?>]
		</option>
	<option value="-"
		<?php

		if ("-" == $_SESSION ["filter_parent"])
			echo "selected";
		?>>
			[<?php

		echo TRANSLATION_NONE;
		?>]
		</option>
		<?php

		while ( $parent = db_fetch_object ( $parents ) ) {
			$parent_id = $parent->id;
			$title = htmlspecialchars ( $parent->title );
			if ($parent_id == $_SESSION ["filter_parent"])
				echo '<option value="' . $parent_id . '" selected>' . $title . "</option>";
			else
				echo '<option value="' . $parent_id . '">' . $title . "</option>";
		}
		?>
	</select>
<?php

		echo TRANSLATION_ENABLED;
		?>
<select name="filter_active" onchange="filter_by_active(this);">
	<option value="null"
		<?php

		if (null == $_SESSION ["filter_active"])
			echo "selected";
		?>>
			[<?php translate("every"); ?>]
		</option>
	<option value="1"
		<?php

		if (1 === $_SESSION ["filter_active"])
			echo "selected";
		?>><?php

		echo TRANSLATION_ENABLED;
		?></option>
	<option value="0"
		<?php

		if (0 === $_SESSION ["filter_active"])
			echo "selected";
		?>><?php

		echo TRANSLATION_DISABLED;
		?></option>
</select>

<?php

  translate("approved");
  ?>
<select name="filter_approved" onchange="filter_by_approved(this);">
<option value="null"
  <?php

  if (null == $_SESSION ["filter_approved"])
    echo "selected";
  ?>>
    [<?php

  translate("every");
  ?>]</option>
<option value="1"
  <?php

  if (1 === $_SESSION ["filter_approved"])
    echo "selected";
  ?>><?php

  translate("yes");
  ?></option>
<option value="0"
  <?php

  if (0 === $_SESSION ["filter_approved"])
    echo "selected";
  ?>><?php

	  translate("no");
  ?></option>
</select>
</p>

<?php
		if ($_SESSION ["filter_status"] == "trash" and $acl->hasPermission ( "pages" )) {
			?>

&nbsp;&nbsp;
<a href="index.php?action=empty_trash"
	onclick="return ajaxEmptyTrash(this.href);">Papierkorb leeren</a>
<?php
		}
		?>

<br />

<table class="tablesorter dataset-list">
	<thead>
		<tr style="font-weight: bold;">
			<th><?php

		echo TRANSLATION_TITLE;
		?>
			</th>
			<th><?php

		echo TRANSLATION_MENU;
		?>
			</th>
			<th><?php

		echo TRANSLATION_POSITION;
		?>
			</th>
			<th><?php

		echo TRANSLATION_PARENT;
		?>
			</th>
			<th><?php

		echo TRANSLATION_ACTIVATED;
		?>
			</th>
			<td style="text-align: center"><?php

		echo TRANSLATION_VIEW;
		?>
			</td>
			<td style="text-align: center"><?php

		translate ( "clone" );
		?>
			</td>
			<td style="text-align: center"><?php

		echo TRANSLATION_EDIT;
		?>
			</td>
			<td style="text-align: center"><?php

		echo TRANSLATION_DELETE;
		?>
			</td>

		</tr>
	</thead>
	<tbody>
	<?php
		if (in_array ( $_GET ["order"], array (
				"title",
				"menu",
				"position",
				"parent",
				"active"
		) ))
			$order = $_GET ["order"];
		$filter_language = basename ( $_GET ["filter_language"] );
		$filter_status = basename ( $_GET ["filter_status"] );

		if (empty ( $filter_language )) {
			if (! empty ( $_SESSION ["filter_language"] )) {
				$filter_language = $_SESSION ["filter_language"];
			} else {
				$filter_language = "";
			}
		}

		if ($_SESSION ["filter_status"] == "trash") {
			$filter_status = "`deleted_at` IS NOT NULL";
		} else {
			$filter_status = "`deleted_at` IS NULL";
		}

		if (empty ( $order )) {
			$order = "menu";
		}

		if (! empty ( $filter_language )) {
			$filter_sql = "WHERE language = '" . $filter_language . "' ";
		} else {
			$filter_sql = "WHERE 1=1 ";
		}

		if ($_SESSION ["filter_category"] != 0) {
			$filter_sql .= "AND category=" . intval ( $_SESSION ["filter_category"] ) . " ";
		}

		$filter_sql .= "AND " . $filter_status . " ";

		if ($_SESSION ["filter_menu"] != null) {
			$filter_sql .= "AND menu = '" . db_escape ( $_SESSION ["filter_menu"] ) . "' ";
		}
		if ($_SESSION ["filter_type"] != null) {
			$filter_sql .= "AND `type` = '" . db_escape ( $_SESSION ["filter_type"] ) . "' ";
		}

		if ($_SESSION ["filter_active"] !== null) {
			$filter_sql .= "AND active = " . intval ( $_SESSION ["filter_active"] ) . " ";
		}


		if ($_SESSION ["filter_approved"] !== null) {
			$filter_sql .= "AND approved = " . intval ( $_SESSION ["filter_approved"] ) . " ";
		}

		if ($_SESSION ["filter_parent"] != null) {
			if ($_SESSION ["filter_parent"] != "-")
				$filter_sql .= "AND parent = '" . intval ( $_SESSION ["filter_parent"] ) . "' ";
			else
				$filter_sql .= "AND parent IS NULL ";
		}

		if (isset ( $_SESSION ["filter_title"] ) and ! empty ( $_SESSION ["filter_title"] )) {
			$filter_sql .= "AND (title LIKE '" . db_escape ( $_SESSION ["filter_title"] ) . "%' or title LIKE '%" . db_escape ( $_SESSION ["filter_title"] ) . "' or title LIKE '%" . db_escape ( $_SESSION ["filter_title"] ) . "%' or title LIKE '" . db_escape ( $_SESSION ["filter_title"] ) . "' )";
		}

		$filter_sql .= " ";

		$query = db_query ( "SELECT * FROM " . tbname ( "content" ) . " " . $filter_sql . " ORDER BY $order,position, systemname ASC" ) or die ( db_error () );
		if (db_num_rows ( $query ) > 0) {
			while ( $row = db_fetch_object ( $query ) ) {
				?>
						<?php

				echo '<tr id="dataset-' . $row->id . '">';
				echo "<td>" . htmlspecialchars ( $row->title );
				if (! empty ( $row->redirection ) and ! is_null ( $row->redirection ) and $row->type == "link") {
					echo htmlspecialchars ( " --> " ) . htmlspecialchars ( $row->redirection );
				}

				echo "</td>";
				echo "<td>" . htmlspecialchars ( get_translation ( $row->menu ) ) . "</td>";

				echo "<td>" . $row->position . "</td>";
				echo "<td>" . htmlspecialchars ( getPageTitleByID ( $row->parent ) ) . "</td>";

				if ($row->active) {
					echo "<td>" . TRANSLATION_YES . "</td>";
				} else {
					echo "<td>" . TRANSLATION_NO . "</td>";
				}

				if (startsWith ( $row->redirection, "#" )) {
					echo "<td style='text-align:center'></td>";
				} else {
					$domain = getDomainByLanguage ( $row->language );
					if (! $domain) {
						$url = "../" . $row->systemname . ".html";
					} else {
						$url = "http://" . $domain . "/" . $row->systemname . ".html";
					}
					echo "<td style='text-align:center'><a href=\"" . $url . "\" target=\"_blank\"><img class=\"mobile-big-image\" src=\"gfx/preview.png\" alt=\"" . TRANSLATION_VIEW . "\" title=\"" . TRANSLATION_VIEW . "\"></a></td>";
				}
				echo "<td style='text-align:center'><a href=\"index.php?action=clone_page&page=" . $row->id . "\"><img class=\"mobile-big-image\" src=\"gfx/clone.png\" alt=\"" . get_translation ( "clone" ) . "\" title=\"" . get_translation ( "clone" ) . "\"></a></td>";

				$autor = $row->autor;
				$is_owner = $autor == get_user_id();

				$pages_edit_own = $acl->hasPermission("pages_edit_own");
				$pages_edit_others = $acl->hasPermission("pages_edit_others");

				$can_edit_this = false;

				if($is_owner and $pages_edit_own){
					 $can_edit_this = true;
				}
				else if(!$is_owner and $pages_edit_others){
					 $can_edit_this = true;
				}

				if(!$can_edit_this){
					 echo "<td></td><td></td>";
				} else {
				echo "<td style='text-align:center'>" . '<a href="index.php?action=pages_edit&page=' . $row->id . '"><img class="mobile-big-image" src="gfx/edit.png" alt="' . TRANSLATION_EDIT . '" title="' . TRANSLATION_EDIT . '"></a></td>';

				if ($_SESSION ["filter_status"] == "trash") {
					/*
					 * echo "<td style='text-align:center'>" . '<a href="index.php?action=undelete_page&page=' . $row -> id . '";"> <img class="mobile-big-image" src="gfx/undelete.png" alt="' . TRANSLATION_RECOVER . '" title="' . TRANSLATION_RECOVER . '"></a></td>';
					 */
					echo "<td style='text-align:center'>" . '<form action="index.php?action=undelete_page&page=' . $row->id . '" method="post" class="undelete-form">' . get_csrf_token_html () . '<input type="image" class="mobile-big-image" src="gfx/undelete.png" alt="' . TRANSLATION_RECOVER . '" title="' . TRANSLATION_RECOVER . '"></form></td>';
				} else {
					/*
					 * echo "<td style='text-align:center'>" . '<a href="index.php?action=pages_delete&page=' . $row -> id . '" onclick="return confirm(\'Wirklich löschen?\');"><img src="gfx/delete.gif" class="mobile-big-image" alt="' . TRANSLATION_DELETE . '" title="' . TRANSLATION_DELETE . '"></a></td>';
					 */
					echo "<td style='text-align:center'>" . '<form action="index.php?action=pages_delete&page=' . $row->id . '" method="post" class="delete-form" onsubmit="return confirm(\'Wirklich löschen?\');">' . get_csrf_token_html () . '<input type="image" src="gfx/delete.gif" class="mobile-big-image" alt="' . TRANSLATION_DELETE . '" title="' . TRANSLATION_DELETE . '"></form></td>';
				}
}
				echo '</tr>';
			}
			?>
		<?php
		}
		?>
	</tbody>
</table>

<script type="text/javascript">

var ajax_options = {
  success : function(responseText, statusText, xhr, $form){
  var action =$($form).attr("action");
  var id = url('?page', action);
  var list_item_id = "dataset-" + id
  var tr = $("tr#" + list_item_id);
  $(tr).fadeOut();

  }


}

$("form.delete-form").ajaxForm(ajax_options);
$("form.undelete-form").ajaxForm(ajax_options);

function ajaxEmptyTrash(url){
   if(confirm("Papierkorb leeren?")){
   $.ajax({
      url: url,
      success: function(){
         $("table.dataset-list tbody tr").fadeOut();
      }
});
}
  return false;
}

</script>


<br />

<?php
	} else {
		noperms ();
	}

	?>

<?php
}
?>
