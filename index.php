<?php

$options = [
	'delay' => 0,
	'preload' => false,
	'preload-delay' => 0,
	'preload-media' => 'all',
	'font-display' => [ 'auto', 'block', 'swap', 'fallback', 'optional' ],
	'font-delay' => 0,
	'font' => [
		null, // Load the font only if requested.
		...array_map(
			fn( $font ) => basename( $font ),
			glob( __DIR__ . '/fonts/*' )
		)
	],
	'image-delay' => 0,
	'image' => [
		null, // Load the image only if requested.
		'screenshot.png',
	],
	'js-enable' => true,
	'js' => false,
	'js-delay' => 1,
	'js-async' => false,
	'js-defer' => false,
];

foreach ( $options as $key => $expected_options ) {
	$value = trim( (string) isset( $_GET[ $key ] ) ? $_GET[ $key ] : '' );

	$options[ $key ] = match ( gettype( $expected_options ) ) {
		'boolean' => strlen( $value ) ? (bool) $value : $expected_options,
		'array' => in_array( $value, $expected_options ) ? $value : current( $expected_options ),
		'integer' => is_numeric( $value ) ? (int) $value : 0,
		default => null,
	};
}

header( 'Cache-Control: no-store, no-cache, must-revalidate' );

if ( $options['delay'] ) {
	sleep( $options['delay'] );
}

if ( $options['js'] ) {
	header( 'Content-Type: text/javascript' );
	echo 'console.log("js")';
	exit;
}

if ( $options['font'] ) {
	$font_file = __DIR__ . '/fonts/' . $options['font'];

	if ( is_readable( $font_file ) ) {
		header( 'Content-Type: font/woff2' );
		header( 'Content-Length: ' . filesize( $font_file ) );
		readfile( $font_file );
		exit;
	}
	
	http_response_code( 404 );
	exit;
}

if ( $options['image'] ) {
	$image_file = sprintf( '%s/%s', __DIR__, $options['image'] );

	if ( is_readable( $image_file ) ) {
		header( sprintf( 'Content-Type: image/%s', pathinfo( $image_file, PATHINFO_EXTENSION ) ) );
		header( 'Content-Length: ' . filesize( $image_file ) );
		readfile( $image_file );
		exit;
	}
	
	http_response_code( 404 );
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Font Preload Testing</title>
	<?php if ( $options['preload'] ) : ?>
	<link rel="preload" href="?font=SourceSerif4Variable-Italic.ttf.woff2&amp;delay=<?php echo $options['preload-delay']; ?>" as="font" type="font/woff2" crossorigin media="<?php $options['preload-media']; ?>" />
	<link rel="preload" href="?font=SourceSerif4Variable-Roman.ttf.woff2&amp;delay=<?php echo $options['preload-delay']; ?>" as="font" type="font/woff2" crossorigin media="<?php $options['preload-media']; ?>" />
	<?php endif; ?>
	<?php if ( $options['js-enable'] ) : ?>
	<script src="?js=1&amp;delay=<?php echo $options['js-delay']; ?>" <?php echo $options['js-async'] ? 'async' : null; ?> <?php echo $options['js-defer'] ? 'defer' : null; ?>></script>
	<?php endif; ?>
	<style>
		@font-face {
			font-family: "Source Serif Pro";
			font-style: italic;
			font-weight: 200 900;
			font-display: <?php echo $options['font-display']; ?>;
			src: url('?font=SourceSerif4Variable-Italic.ttf.woff2&delay=<?php echo $options['font-delay']; ?>') format('woff2');
			font-stretch: normal;
		}
		@font-face {
			font-family: "Source Serif Pro";
			font-style: normal;
			font-weight: 200 900;
			font-display: <?php echo $options['font-display']; ?>;
			src: url('?font=SourceSerif4Variable-Roman.ttf.woff2&delay=<?php echo $options['font-delay']; ?>') format('woff2');
			font-stretch: normal;
		}
		body { 
			font: 22px/1.5 system-ui, sans-serif;
		}
		h1, h2, h3, h4, h5, h6 {
			font-family: "Source Serif Pro", sans-serif;
		}
		img {
			max-width: 100%;
		}
	</style>
</head>
<body>
	<h1>Font <em>Preload Test 🦫 with a <strong>Really</strong> Long Title to Ensure</em> it is the <strong>Largest Content</strong></h1>
	<ul>
		<li>
			<a href="?preload=true">Preload</a> 
			<a href="?preload=true&amp;preload-delay=2">Preload 2s delay</a> 
			<a href="?preload=true&amp;preload-delay=2&amp;font-delay=2">Preload and font 2s delay</a>
		</li>
		<li>
			<a href="?preload=false">No Preload</a>
			<a href="?preload=false&amp;font-delay=2">Font 2s delay</a> 
		</li>
	</ul>
	<p>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus pariatur 
		cupiditate repellendus porro, tempora rerum iusto. Eum blanditiis officia enim 
		commodi doloremque sit dolores, modi ullam corporis ea eveniet dolore!
	</p>
	<p>
		<img src="?image=screenshot.png&amp;delay=<?php echo $options['image-delay']; ?>" alt="Screenshot of a test" />
	</p>
</body>
</html>