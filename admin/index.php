<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://www.xoops.org>                             //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
include '../../../include/cp_header.php';
require XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$op = 'default';
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}
if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$myts = MyTextSanitizer::getInstance();

switch ($op) {
    case 'add':

        xoops_cp_header();
        $form = new XoopsThemeForm(_AM_ADDCONTACT, 'form', 'index.php');
        $form->addElement(new XoopsFormSelectUser(_AM_CONTACTNAME, 'user', false, null, 5, false));
        $form->addElement(new XoopsFormText(_AM_CONTENT, 'content', 20, 30), true);
        $form->addElement(new XoopsFormText(_AM_PHONE_NUMBER, 'phone_number', 20, 30), true);
        $form->addElement(new XoopsFormHidden('op', 'record'), true);
        $form->addElement(new XoopsFormButton('', 'submit', _AM_ENREG, 'submit'), true);
        $form->display();
        xoops_cp_footer();

        break;
    case 'list':

        xoops_cp_header();
        $query = 'select uid from ' . $xoopsDB->prefix('urgence') . ' group by uid';
        $result = $xoopsDB->queryF($query);
        $form = new XoopsThemeForm(_AM_CHOOSECONTACT, 'form', 'index.php');
        $select = new XoopsFormSelect(_AM_CONTACTNAME, 'user', null, 5, false);
        while (false !== ($user = $xoopsDB->fetchArray($result))) {
            $select->addOption($user['uid'], XoopsUser::getUnameFromId($user['uid']));
        }
        $form->addElement($select);
        $form->addElement(new XoopsFormHidden('op', 'datalist'), true);
        $form->addElement(new XoopsFormButton('', 'submit', _AM_MODIFY, 'submit'), true);
        $form->display();
        xoops_cp_footer();

        break;
    case 'datalist':
        xoops_cp_header();
        $query = 'select id,content,phone_number from ' . $xoopsDB->prefix('urgence') . " where uid=$user order by id";
        $result = $xoopsDB->queryF($query);
        $form = new XoopsThemeForm(_AM_MODIFY . ' ' . XoopsUser::getUnameFromId($user), 'form', 'index.php');
        $i = 0;
        while (false !== ($data = $xoopsDB->fetchArray($result))) {
            $form = new XoopsThemeForm(_AM_MODIFY, 'form', 'index.php');

            $form->addElement(new XoopsFormHidden('op', 'modify'), true);

            $form->addElement(new XoopsFormHidden('user', $user), true);

            $form->addElement(new XoopsFormHidden('id', $data['id']), true);

            $container[$i] = new XoopsFormElementTray($data['id']);

            $container[$i]->addElement(new XoopsFormText(_AM_CONTENT, 'content', 20, 30, $data['content']), true);

            $container[$i]->addElement(new XoopsFormText(_AM_PHONE_NUMBER, 'phone_number', 20, 30, $data['phone_number']), true);

            $container[$i]->addElement(new XoopsFormButton('', 'modify', _AM_MODIFY, 'submit'));

            $container[$i]->addElement(new XoopsFormButton('', 'delete', _AM_DELETE, 'submit'));

            $form->addElement($container[$i]);

            $form->display();

            $i++;
        }
        xoops_cp_footer();
        break;
    case 'modify':
        if ('' != $modify) {
            $content = $myts->previewTarea($content, 0, 0, 0, 0, 0);

            $phone_number = $myts->previewTarea($phone_number, 0, 0, 0, 0, 0);

            $query = 'update ' . $xoopsDB->prefix('urgence') . " set content='$content',phone_number='$phone_number' where id=$id";

            $result = $xoopsDB->queryF($query);

            redirect_header("index.php?op=datalist&user=$user", 1, _AM_ENREGOK);
        } elseif ('' != $delete) {
            $query = 'delete from ' . $xoopsDB->prefix('urgence') . " where id=$id";

            $result = $xoopsDB->queryF($query);

            redirect_header('index.php', 1, _AM_ENREGOK);
        }
        break;
    case 'record':

        $user = $myts->previewTarea($user, 0, 0, 0, 0, 0);
        $content = $myts->previewTarea($content, 0, 0, 0, 0, 0);
        $phone_number = $myts->previewTarea($phone_number, 0, 0, 0, 0, 0);

        $query = 'INSERT INTO `' . $xoopsDB->prefix('urgence') . "` ( `id` , `uid` , `content` , `phone_number` ) VALUES ('', $user, '$content', '$phone_number');";
        if ($xoopsDB->queryF($query)) {
            redirect_header('index.php', 1, _AM_ENREGOK);
        } else {
            redirect_header('index.php', 1, _AM_ENREGNOK);
        }
        break;
    // display default menu
    case 'default':
    default:

        xoops_cp_header();
        echo '<h4>' . _AM_TITLECONTACT . '</h4>';
        echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo " - <b><a href='index.php?op=list'>" . _AM_ADMENU2 . '</a></b>';
        echo "<br><br>\n";
        echo " - <b><a href='index.php?op=add'>" . _AM_ADMENU3 . "</a></b>\n";
        echo '</td></tr></table>';
        xoops_cp_footer();

        break;
}
