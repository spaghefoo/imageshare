<!DOCTYPE html>

<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ImageShare</title>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <!-- Custom tile for Internet Explorer and Edge -->
    <meta name="application-name" content="ImageShare" />
    <meta name="msapplication-config" content="/ieconfig.xml" />
    <!-- Metadata for old iPhone and iPad devices -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" sizes="192x192" href="/img/maskable_icon_x192.png">
    <meta name="apple-mobile-web-app-title" content="ImageShare">
    <!-- Viewport size and analytics -->
    <?php
    // Viewport size
    $is3DS = strpos($_SERVER['HTTP_USER_AGENT'], 'Nintendo 3DS');
    $isNew3DS = strpos($_SERVER['HTTP_USER_AGENT'], 'New Nintendo 3DS');
    if ($is3DS && !($isNew3DS)) {
      // This is required for proper viewport size on old 3DS web browser
      echo '<meta name="viewport" content="width=320" />'.PHP_EOL;
    } else {
      // Normal mobile scaling for New 3DS Browser and everything else
      echo '<meta name="viewport" content="width=device-width, initial-scale=1">'.PHP_EOL;
    }
    // Send Plausible analytics data for pageview
    if (str_contains($_SERVER['HTTP_HOST'], 'theimageshare.com')) {
      $data = array(
        'name' => 'pageview',
        'url' => 'https://theimageshare.com/',
        'domain' => 'theimageshare.com',
      );
      $post_data = json_encode($data);
      // Prepare new cURL resource
      $crl = curl_init('https://plausible.io/api/event');
      curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($crl, CURLINFO_HEADER_OUT, true);
      curl_setopt($crl, CURLOPT_POST, true);
      curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
      // Set HTTP Header for POST request 
      curl_setopt($crl, CURLOPT_HTTPHEADER, array(
        'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'],
        'X-Forwarded-For: 127.0.0.1',
        'Content-Type: application/json')
      );
      // Submit the POST request
      $result = curl_exec($crl);
      curl_close($crl);
    }
    ?>
    <!-- Standard icons -->
    <?php
    // Use a 16x16 favicon for the 3DS and Wii, larger icons in multiple sizes for other browsers
    if (str_contains($_SERVER['HTTP_USER_AGENT'], 'Nintendo')) {
      echo '<link rel="icon" href="favicon.ico" type="image/x-icon">'.PHP_EOL;
    } else {
      echo '<link rel="icon" type="image/png" sizes="16x16" href="img/favicon_x16.png">'.PHP_EOL;
      echo '    <link rel="icon" type="image/png" sizes="24x24" href="img/favicon_x24.png">'.PHP_EOL;
    }
    ?>
    <!-- Twitter card -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:creator" content="@corbindavenport" />
    <meta name="twitter:title" content="ImageShare" />
    <meta name="twitter:description" content="Lightweight web app for uploading images, created for the Nintendo 3DS and other legacy web browsers." />
    <meta name="twitter:image" content="https://theimageshare.com/img/maskable_icon_x512.png" />
    <meta name="twitter:image:alt" content="ImageShare app icon" />
    <!-- OpenGraph card -->
    <meta property="og:url" content="https://theimageshare.com/" />
    <meta property="og:title" content="ImageShare" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="Lightweight web app for uploading images, created for the Nintendo 3DS and other legacy web browsers." />
    <meta property="og:image" content="https://theimageshare.com/img/maskable_icon_x512.png" />
</head>

<body>

    <div class="header">ImageShare</div>

    <div class="container">

    <?php

    if(isset($_POST['submit'])){
        // Set curl info
        $delete_hash = $_POST['id'];
        $curl = curl_init();
        $client = getenv('API_KEY');
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.imgur.com/3/image/'.$delete_hash,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'DELETE',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Client-ID '.$client
          ),
        ));
        // Delete the image
        $output = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        // Show output
        if ($status === 200) {
            $html = '
                <div class="panel">
                    <div class="panel-title">Image deleted</div>
                    <div class="body">
                        <p>Your image has been successfully deleted.</p>
                        <p><a href="/">Upload new image</a></p>
                    </div>
                </div>
            ';
            echo $html;
        } else {
            $html = '
                <div class="panel">
                    <div class="panel-title">Error</div>
                    <div class="body">
                        <p>There was an error with the API, your image was not deleted.</p>
                        <p><a href="/">Upload new image</a></p>
                    </div>
                </div>
            ';
            echo $html;
        }
    }

    ?>
        
    </div>

</body>
</html>