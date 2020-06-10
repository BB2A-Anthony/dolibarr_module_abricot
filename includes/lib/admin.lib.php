<?php
/*
 Copyright (C) 2003-2013 Alexis Algoud <azriel68@gmail.com>
 Copyright (C) 2013-2015 ATM Consulting <support@atm-consulting.fr>

 This program and all files within this directory and sub directory
 is free software: you can redistribute it and/or modify it under
 the terms of the GNU General Public License as published by the
 Free Software Foundation, either version 3 of the License, or any
 later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.

 This is a library for module setup page

 */

/**
 * Display title
 * @param string $title
 */
function setup_print_title($title="", $width = 300)
{
    global $langs;
    print '<tr class="liste_titre">';
    print '<th colspan="3">'.$langs->trans($title).'</th>'."\n";
    print '</tr>';
}


//
// _print_on_off('CONSTNAME', 'ParamLabel' , 'ParamDesc');

/**
 * yes / no select
 * @param string $confkey
 * @param string $title
 * @param string $desc
 * @param $ajaxConstantOnOffInput will be send to ajax_constantonoff() input param
 *
 * exemple _print_on_off('CONSTNAME', 'ParamLabel' , 'ParamDesc');
 */
function setup_print_on_off($confkey, $title = false, $desc ='', $help = false, $width = 300, $forcereload = false, $ajaxConstantOnOffInput = array())
{
    global $var, $bc, $langs, $conf, $form;
    $var=!$var;

    print '<tr '.$bc[$var].'>';
    print '<td>';


	if(empty($help) && !empty($langs->tab_translate[$confkey . '_HELP'])){
		$help = $confkey . '_HELP';
	}

    if(!empty($help)){
        print $form->textwithtooltip( ($title?$title:$langs->trans($confkey)) , $langs->trans($help),2,1,img_help(1,''));
    }
    else {
        print $title?$title:$langs->trans($confkey);
    }

    if(!empty($desc))
    {
        print '<br><small>'.$langs->trans($desc).'</small>';
    }
    print '</td>';
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="center" width="'.$width.'">';

    if($forcereload){
        $link = $_SERVER['PHP_SELF'].'?action=set_'.$confkey.'&token='.$_SESSION['newtoken'].'&'.$confkey.'='.intval((empty($conf->global->{$confkey})));
        $toggleClass = empty($conf->global->{$confkey})?'fa-toggle-off':'fa-toggle-on font-status4';
        print '<a href="'.$link.'" ><span class="fas '.$toggleClass.' marginleftonly" style=" color: #999;"></span></a>';
    }
    else{
        print ajax_constantonoff($confkey, $ajaxConstantOnOffInput);
    }
    print '</td></tr>';
}


/**
 * Auto print form part for setup
 * @param string $confkey
 * @param bool $title
 * @param string $desc
 * @param array $metas exemple use with color array('type'=>'color') or  with placeholder array('placeholder'=>'http://')
 * @param string $type = 'imput', 'textarea' or custom html
 * @param bool $help
 * @param int $width
 */
function setup_print_input_form_part($confkey, $title = false, $desc ='', $metas = array(), $type='input', $help = false, $width = 300)
{
    global $var, $bc, $langs, $conf, $db;
    $var=!$var;

    $form=new Form($db);

    $defaultMetas = array(
        'name' => $confkey
    );

    if($type!='textarea'){
        $defaultMetas['type']   = 'text';
        $defaultMetas['value']  = $conf->global->{$confkey};
    }


    $metas = array_merge ($defaultMetas, $metas);
    $metascompil = '';
    foreach ($metas as $key => $values)
    {
        $metascompil .= ' '.$key.'="'.$values.'" ';
    }

    print '<tr '.$bc[$var].'>';
    print '<td>';

    if(!empty($help)){
        print $form->textwithtooltip( ($title?$title:$langs->trans($confkey)) , $langs->trans($help),2,1,img_help(1,''));
    }
    else {
        print $title?$title:$langs->trans($confkey);
    }

    if(!empty($desc))
    {
        print '<br><small>'.$langs->trans($desc).'</small>';
    }

    print '</td>';
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="right" width="'.$width.'">';
    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'" '.($metas['type'] === 'file' ? 'enctype="multipart/form-data"' : '').'>';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="set_'.$confkey.'">';

		if($type=='textarea'){
			print '<textarea '.$metascompil.'  >'.dol_htmlentities($conf->global->{$confkey}).'</textarea>';
		}
		elseif($type=='input'){
			print '<input '.$metascompil.'  />';
		}
		else{
			// custom
			print $type;
		}

    print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
    print '</form>';
    print '</td></tr>';
}
