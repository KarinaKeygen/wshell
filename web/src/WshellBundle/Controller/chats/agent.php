<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// $_POST = array( 'channel'=>xxx, 'event'=>yyy, 'data'=>zzz );
if ($_POST['event'] == 'say' && $user->has('name')) {
    $fileName = '../logs/' . date('Ymd') . $_POST['channel'] . '_agent.log';
    file_put_contents($fileName, date('h:i:s') . ' ' . $_SESSION['name'] . ': ' . htmlspecialchars($_POST['data']) . "\r\n", FILE_APPEND);
}

$wshell->initRedbean();
$chat = R::findOne('chat', ' name = ? ', array('offtop'));


//$chat->link('chat_user', array('role'=>'user'))->user = $userd;
//R::store($chat);

$app_id     = '59608';
$app_key    = '9d6e6d30b90efdbc3232';
$app_secret = 'd278f2f9c242d357f6ee';

// key, secret, id, [debug_mode, ]
$pusher = new Pusher($app_key, $app_secret, $app_id);

// proccesing command
switch($_POST['event']) {
    case 'users':
        $users = $chat->sharedUser;
        foreach($users as $user) {
            $userNames[$user->id] = $user->name;
        }
        if (!empty($userNames)) {
            $pusher->trigger('presence-' . $_POST['channel'], 'users', $userNames);
        }
        break;
    case 'say':
        if ($user->has('name')) {
            $pusher->trigger('presence-' . $_POST['channel'], 'say', $_SESSION['name'] . ': ' . htmlspecialchars($_POST['data']));
        }
        break;
    case 'wisper':
        echo "tsssss";
        break;
    case 'story':
        echo "blablabla";
        break;
    case 'last':
        //echo file_get_contents('../logs/' . date('Ymd') . $_POST['channel'] . '_agent.log');
        break;
}