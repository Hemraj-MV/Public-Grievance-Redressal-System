<?php
// ai_processor.php

function analyzeWithAI($description) {
    $url = "http://localhost:11434/api/generate";
    
    // We strictly tell the AI what we want to prevent "hallucinations"
    $prompt = $prompt = $prompt = "You are a highly senior Government Administrative Officer responsible for triaging public petitions.
Analyze this petition: '$description'

CHOOSE EXACTLY ONE CATEGORY FROM THIS MASTER LIST:
1. 'Water & Sewage': Pipeline leaks, water shortage, drainage blockage, open manholes.
2. 'Electricity': Power cuts, street lights not working, sparking transformers, hanging wires.
3. 'Roads & Infrastructure': Potholes, broken footpaths, bridge issues, flooded roads.
4. 'Public Health': Hospitals, disease outbreaks, birth/death certificates, food safety.
5. 'Sanitation': Garbage collection, public toilets, smelly landfills, street sweeping.
6. 'Public Safety': Noise pollution (loud neighbors/parties), stray animal menace, illegal activities, traffic issues.
7. 'Revenue & Land': Property tax, land disputes, caste/income certificates, encroachment.
8. 'Education': Government schools, scholarships, mid-day meals.
9. 'Social Welfare': Pensions, disability aid, women/child welfare programs.
10. 'General': Anything that doesn't fit the above.

PRIORITY RULES:
- 'Urgent': Immediate life threat, electrical fire, major flooding, or serious crime in progress.
- 'High': Major service outage (no water/power for days), blocked main roads.
- 'Medium': Routine delays, noise complaints, certificate issues, garbage not picked.
- 'Low': General suggestions, non-urgent inquiries.

Respond ONLY in valid JSON: {\"category\": \"\", \"priority\": \"\"}";

    $data = [
        "model" => "llama3.2", // Make sure this matches the model you pulled
        "prompt" => $prompt,
        "stream" => false,
        "format" => "json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        return ["category" => "Error", "priority" => "Unknown"];
    }
    
    curl_close($ch);

    $result = json_decode($response, true);
    // The actual AI text is inside the 'response' key of the Ollama JSON
    return json_decode($result['response'], true);
}
?>