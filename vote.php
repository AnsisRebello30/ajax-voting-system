<?php
// Path to the XML file where votes are stored
$xml_file = 'votes.xml';

// Load the existing XML file, or create a new one if it doesn't exist
if (file_exists($xml_file)) {
    $xml = simplexml_load_file($xml_file);
} else {
    // Create a new XML structure if the file doesn't exist
    $xml = new SimpleXMLElement('<items></items>');
}

// Get the item ID and vote type from the AJAX POST request
$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$vote_type = isset($_POST['vote_type']) ? $_POST['vote_type'] : '';

// Find the item in the XML file or create a new one if it doesn't exist
$item = $xml->xpath("//item[@id='$item_id']")[0] ?? null;

if (!$item) {
    // If the item doesn't exist, create a new one
    $item = $xml->addChild('item');
    $item->addAttribute('id', $item_id);
    $item->addChild('upvotes', 0);
    $item->addChild('downvotes', 0);
}

// Update the vote count based on the vote type
if ($vote_type === 'up') {
    $item->upvotes = (int)$item->upvotes + 1;
} elseif ($vote_type === 'down') {
    $item->downvotes = (int)$item->downvotes + 1;
}

// Save the updated XML file
$xml->asXML($xml_file);

// Return the updated vote counts as JSON
$response = [
    'upvotes' => (int)$item->upvotes,
    'downvotes' => (int)$item->downvotes
];

header('Content-Type: application/json');
echo json_encode($response);
?>
