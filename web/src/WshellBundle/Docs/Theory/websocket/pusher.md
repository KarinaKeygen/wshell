/*
$chats = ['offtop', 'private-offtop', 'presence-offtop'];
$json = ['public message', 'private message', 'presence message'];

// channel, event, data, [socket_id, debug_mode, already_json]
$pusher->trigger($chats[0], 'new_message', $json[0] );
$pusher->trigger($chats[1], 'new_message', $json[1] );
$pusher->trigger($chats[2], 'new_message', $json[2] );
*/
// private channel
//$pusher->socket_auth('my-channel','socket_id');
// or (flexibility)
//$pusher->presence_auth('my-channel','socket_id', 'user_id', 'user_info');



// Application State Queries

// get list occupied channels
/*
$response = $pusher->get( '/channels' );
$http_status_code = $response[ 'status' ];
$result = $response[ 'result' ];
*/

// info about channel
/*
$info = $pusher->get('/channels/channel-name');
$channel_occupied = $info->occupied;
*/

// get all channels
/*
$result = $pusher->get_channels();
$channel_count = count($result->channels);
*/

// filtering channels
/*
$results = $pusher->get_channels( array( 'filter_by_prefix' => 'presence-') );
$channel_count = count($result->channels);

or

$pusher->get( '/channels', array( 'filter_by_prefix' => 'presence-' ) );
*/

// user info !!!
// $response = $pusher->get( '/channels/presence-channel-name/users' );





// WebHooks - getting notified about events (events from pusher)
// contain headers: X-Pusher-Key and X-Pusher-Signature
/*
http://pusher/occupied
http://pusher/subscribes
*/