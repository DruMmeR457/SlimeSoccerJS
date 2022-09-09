# SlimeSoccerJS
Old Slime Soccer Flash game rewritten in JS

## Files
_file_<int> - The messages left for a client. The <int> is their client ID (currently hard-coded to 1 and 2)
pairings.db - mapping of game host client to game joiner client
serverPost.php - How clients write messages to their partner's _file_<int>
serverGet.php - How clients read messages from their _file_<int>
clear.php - Clears out pairings.db and all _file_<int> files, essentially closing all games and sessions
