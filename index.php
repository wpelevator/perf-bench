<?php

header( 'Cache-Control: no-store, no-cache, must-revalidate' );

$delay_ms = isset( $_GET['delay'] ) ? min( (int) $_GET['delay'], 10 ) : 0;

if ( $delay_ms ) {
	sleep( $delay_ms );
}

$fonts = [
	'SourceSerif4Variable-Italic.ttf.woff2',
	'SourceSerif4Variable-Roman.ttf.woff2',
];

if ( isset( $_GET['font'] ) ) {
	if ( in_array( $_GET['font'], $fonts ) ) {
		header( 'Content-Type: font/woff2' );
		readfile( __DIR__ . '/fonts/' . $_GET['font'] );
		exit;
	}

	http_response_code( 404 );
	exit;
}

$options = [
	'preload' => false,
	'preload-delay' => 0,
	'font-display' => [ 'auto', 'block', 'swap', 'fallback', 'optional' ],
	'font-delay' => 0,
];

foreach ( $options as $key => $expected_options ) {
	$value = trim( (string) isset( $_GET[ $key ] ) ? $_GET[ $key ] : '' );

	$options[ $key ] = match ( gettype( $expected_options ) ) {
		'boolean' => (bool) $value,
		'array' => in_array( $value, $expected_options ) ? $value : current( $expected_options ),
		'integer' => is_numeric( $value ) ? (int) $value : 0,
		default => null,
	};
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Font Preload Testing</title>
	<?php if ( $options['preload'] ) : ?>
	<link rel="preload" href="?font=SourceSerif4Variable-Italic.ttf.woff2&delay=<?php echo $options['preload-delay']; ?>" as="font" type="font/woff2" crossorigin />
	<link rel="preload" href="?font=SourceSerif4Variable-Roman.ttf.woff2&delay=<?php echo $options['preload-delay']; ?>" as="font" type="font/woff2" crossorigin />
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
			font-family: "Source Serif Pro", serif;
		}
	</style>
</head>
<body>
	<h1>Font <em>Preload</em> Testing</h1>
	<ul>
		<li>
			<a href="?preload=true">Preload</a> 
			<a href="?preload=true&preload-delay=2">Preload 2s delay</a> 
			<a href="?preload=true&preload-delay=2&font-delay=2">Preload and font 2s delay</a>
		</li>
		<li>
			<a href="?preload=false">No Preload</a>
			<a href="?preload=false&font-delay=2">Font 2s delay</a> 
		</li>
	</ul>
	<p>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus pariatur 
		cupiditate repellendus porro, tempora rerum iusto. Eum blanditiis officia enim 
		commodi doloremque sit dolores, modi ullam corporis ea eveniet dolore!
	</p>
</body>
</html>