<?php
/* Template for tam_pdf posts: clean redirect to open PDF in new tab */
$pdf_url = get_post_meta(get_the_ID(), 'tam_pdf_link', true);

if ($pdf_url) :
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo esc_html(get_the_title()); ?></title>
    <meta name="robots" content="noindex, nofollow">
    <script>
        window.onload = function() {
            window.open("<?php echo esc_url($pdf_url); ?>", "_blank");
             setTimeout(function() {
                window.location.href = "<?php echo esc_url(home_url()); ?>"; 
            }, 800);
        };
    </script>
    <style>
        body {
            background: #f9f9f9;
            font-family: sans-serif;
            text-align: center;
            padding: 3em;
        }
    </style>
</head>
<body>
    <p>Opening PDF... <a href="<?php echo esc_url($pdf_url); ?>" target="_blank">Click here if it doesn't open.</a></p>

</html>
<?php
else :
    wp_redirect(home_url());
    exit;
endif;
