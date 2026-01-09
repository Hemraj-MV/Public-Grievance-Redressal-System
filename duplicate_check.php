<?php
function findDuplicate($conn, $new_description) {
    // 1. RETRIEVAL: Get the 5 most recent petitions from the same day/week
    // We use a simple keyword search to find potential matches
    $first_word = explode(' ', trim($new_description))[0];
    $sql = "SELECT id, description FROM petitions WHERE description LIKE '%$first_word%' ORDER BY id DESC LIMIT 5";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $existing_desc = $row['description'];
            
            // 2. AUGMENTATION & GENERATION: Ask AI if these two are the same issue
            $check_prompt = "Compare these two petitions:
            1: '$existing_desc'
            2: '$new_description'
            
            Are they reporting the exact same physical issue or event? 
            Respond with ONLY 'YES' or 'NO'.";

            // (Call Ollama here - simplified for logic)
            $is_match = callOllamaSimple($check_prompt); 

            if (trim($is_match) == "YES") {
                return $row['id']; // Return the ID of the original petition
            }
        }
    }
    return null; // No duplicate found
}

// Helper for simple text response from Ollama
function callOllamaSimple($prompt) {
    $url = "http://localhost:11434/api/generate";
    $data = ["model" => "llama3.2", "prompt" => $prompt, "stream" => false];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($ch), true);
    return strtoupper(trim($response['response']));
}
?>