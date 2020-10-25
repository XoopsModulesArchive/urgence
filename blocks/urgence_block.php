<?php

function urgence_show()
{
    global $xoopsDB, $xoopsUser;

    $block = [];

    $query = 'select uid,content,phone_number from ' . $xoopsDB->prefix('urgence') . ' order by id';

    $result = $xoopsDB->query($query);

    $olduid = 0;

    $i = -1;

    while (false !== ($data = $xoopsDB->fetchArray($result))) {
        $userdata = [];

        $userdata['content'] = $data['content'];

        $userdata['phone_number'] = $data['phone_number'];

        if ($data['uid'] != $olduid) {
            $i++;

            $olduid = $data['uid'];

            $block[$i]['user'] = XoopsUser::getUnameFromId($data['uid']);

            $block[$i]['uid'] = $data['uid'];

            $block[$i]['data'] = [];
        }

        $block[$i]['data'][] = $userdata;
    }

    return $block;
}
