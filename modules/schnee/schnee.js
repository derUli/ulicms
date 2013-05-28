//
// Copyright (C) 2008 by Fabian Schlieper
// Schnee v1.2
// http://www.fabi.me/
// Ohne dieses Copyright darf dieser Code nicht verwendet werden!
//

// die ID des HTML-Elements, in dem es schneien soll

document.body.id = 'winter';

var snow_area_id = 'winter';

// maximale Anzahl der sichtbaren Schneeflocken
var snow_flake_count = 50;

// die verschiedenen Farbe, die die Schneeflocken haben sollen
var snow_colors = new Array('#AAAACC','#DDDDFF','#CCCCDD','#F3F3F3','#F0FFFF');

// die Schriftarten, aus denen die Schneeflocken bestehen sollen
var snow_fonts  = new Array('Arial Black', 'Arial Narrow', 'Times', 'Comic Sans MS');

var snow_char = '*';		// das Zeichen, das als Schneeflocke verwendet wird
var snow_gravity = 0.8;		// wie schnell die Schneeflocken fallen
var snow_max_size = 22;		// die maximale Schriftgröße einer Schneeflocke
var snow_min_size = 10;		// die minimale Schriftgröße einer Schneeflocke

var snow_init_delay = 50;	// Verzögerungszeit in Millisekunden, bevor es zu schneien anfängt


// ######################################################################
// HIER ENDET DIE KONFIGURATION. ÄNDERUNGEN IM FOLGENDEN SETZEN JAVSCRIPT-KENNTNISSE VORAUS
// ######################################################################

var snow_init_time = 0;
var snowflakes = new Array();
var snow_area_el = null;

function createSnowflakes()
{
	var style = 'position:absolute; top:-' + snow_max_size + 'px; z-index:99;';
	for (var i = 0; i <= snow_flake_count; i++)
		document.write('<sp' + 'an id="snwflk' + i + '" style="' + style + '">' + snow_char + '</sp' + 'an>');
}

function randInt(range) { return Math.floor(Math.random() * range); }

function initSnow()
{	
	snow_area_el = document.getElementById(snow_area_id);
	
	// reapeat until we have the snow_area_el
	if(!snow_area_el || snow_area_el.offsetWidth <= snow_max_size || snow_area_el.offsetHeight <= snow_max_size) {
		// after 5 secs cancel
		if(snow_init_time < 5000)
			window.setTimeout('initSnow()', 50);
			
		snow_init_time += 50;
		return;
	}
	
	// offest fix
	snow_area_el.style.position = 'relative';	
			
	for (var i = 0; i <= snow_flake_count; i++)
	{	
		snowflakes[i] = document.getElementById('snwflk' + i);
		
		snowflakes[i].size = (randInt(snow_max_size - snow_min_size) + snow_min_size);		
		snowflakes[i].posx = -snowflakes[i].size;
		snowflakes[i].posy = -snowflakes[i].size;
		snowflakes[i].sink = (snow_gravity * snowflakes[i].size / snow_min_size);
		snowflakes[i].wobamp = (Math.random() * (snowflakes[i].size));
		snowflakes[i].wob = 0.0;
		snowflakes[i].wobspeed = (0.03 + Math.random() / 10.0);
		
		snowflakes[i].style.fontFamily = snow_fonts[randInt(snow_fonts.length)];
		snowflakes[i].style.fontSize = snowflakes[i].size + 'px';
		snowflakes[i].style.color = snow_colors[randInt(snow_colors.length)];
	}
	
	window.setInterval('updateSnow()', 50);
}

function updateSnow()
{
	var bl = snow_area_el.offsetLeft;
	var bt = snow_area_el.offsetTop;
	var bw = snow_area_el.offsetWidth;
	var bh = snow_area_el.offsetHeight;	
	var br = bl + bw;	
	var bb = bt + bh;	
		
	for (var i = 0; i <= snow_flake_count; i++)
	{
		snowflakes[i].wob += snowflakes[i].wobspeed;
		var x = snowflakes[i].posx + (snowflakes[i].wobamp * Math.sin(snowflakes[i].wob));		
		snowflakes[i].posy += snowflakes[i].sink;
		
		snowflakes[i].style.left = Math.round(x) + 'px';
		snowflakes[i].style.top = Math.round(snowflakes[i].posy) + 'px';
		
		var s = snowflakes[i].size;		
		// check bounds
		if (snowflakes[i].posy > (bb - s) || x < bl || x  > (br - s))
		{
			snowflakes[i].posx = bl + s + randInt(bw - (3 * s));
			
			if(snowflakes[i].posy < 0)
				snowflakes[i].posy = bt + randInt(bh - 2 * s);
			else
				snowflakes[i].posy = bt;
		}
	}
}

// Schnee initialisieren
createSnowflakes();
window.setTimeout('initSnow()', Math.max(50, snow_init_delay));
