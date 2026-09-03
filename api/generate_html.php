<?php
/**
 * API Endpoint to Generate Email HTML
 *
 * Receives email data as a JSON payload, constructs the full HTML,
 * and returns it in a JSON response.
 */

require_once __DIR__ . '/../includes/init.php';

$jsonPayload = file_get_contents('php://input');
$data = json_decode($jsonPayload, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_json_response(false, 'Invalid JSON payload received.', ['json_error' => json_last_error_msg()], 400);
    exit;
}

if (!isset($data['article_blocks']) || !is_array($data['article_blocks'])) {
    send_json_response(false, 'Missing or invalid article_blocks data.', null, 400);
    exit;
}

function generate_email_html($data) {
    $primaryColor = PRIMARY_BRAND_COLOR;
    $fontFamily = DEFAULT_FONT_FAMILY;

    $subject = htmlspecialchars($data['subject'] ?? 'No Subject');
    $preheader = htmlspecialchars($data['preheader'] ?? '');
    $logoUrl = htmlspecialchars($data['logo_url'] ?? DEFAULT_LOGO_URL);

    $articlesHtml = '';
    foreach ($data['article_blocks'] as $block) {
        $title = htmlspecialchars($block['title'] ?? '');
        $body = $block['body'] ?? '';
        $ctaText = htmlspecialchars($block['cta_text'] ?? '');
        $ctaUrl = htmlspecialchars($block['cta_url'] ?? '#');
        $imageUrl = htmlspecialchars($block['image_url'] ?? '');

        if (($block['type'] ?? '') === 'single') {
             $articlesHtml .= "
                <h2 style='color: {$primaryColor};'>{$title}</h2>
                " . (!empty($imageUrl) ? "<p><img src='{$imageUrl}' alt='Article Image' style='max-width: 100%; height: auto;'/></p>" : "") . "
                <div>{$body}</div>
                " . (!empty($ctaText) ? "<a href='{$ctaUrl}' style='color: {$primaryColor};'>{$ctaText}</a>" : "") . "
                <hr/>
             ";
        }
    }

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>{$subject}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: {$fontFamily}, sans-serif; }
    </style>
</head>
<body style="margin: 0; padding: 20px; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 20px;">
        <p style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">{$preheader}</p>
        <img src="{$logoUrl}" alt="Logo" style="max-width: 150px; margin-bottom: 20px;">
        {$articlesHtml}
    </div>
</body>
</html>
HTML;
}

$generatedHtml = generate_email_html($data);

send_json_response(true, 'HTML generated successfully.', [
    'html' => $generatedHtml
]);
