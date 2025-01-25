<?php

// Sync these with index.php.
$options = [
	'preload' => [ 1, 0 ],
	'preload-delay' => [ 0, 1, 2 ],
	'font-display' => [ 'auto', 'block', 'swap', 'fallback', 'optional' ],
	'font-delay' => [ 0, 1, 2 ],
];

function array_cartesian_product( $arrays ) {
	$result = array();
	$arrays = array_values($arrays);
	$sizeIn = sizeof($arrays);
	
	$size = $sizeIn > 0 ? 1 : 0;

	foreach ($arrays as $array) {
		$size = $size * sizeof($array);
	}

	for ($i = 0; $i < $size; $i ++) {
		$result[$i] = array();
		for ($j = 0; $j < $sizeIn; $j ++) {
			array_push($result[$i], current($arrays[$j]));
		}
		for ($j = ($sizeIn -1); $j >= 0; $j --) {
			if (next($arrays[$j]))
				break;
			elseif (isset ($arrays[$j]))
				reset($arrays[$j]);
		}
	}
	
	return $result;
}

foreach ( $options as $key => $values ) {
	$options[ $key ] = array_map(
		fn( $value ) => [ $key => $value ],
		$values
	);
}

$url = isset( $argv[1] ) ? rtrim( $argv[1], '/' ) : null;

$combinations = array_map(
	fn( $combination ) => sprintf( '%s/?%s', $url, http_build_query( array_merge( ...$combination ) ) ),
	array_cartesian_product( $options )
);

echo implode( PHP_EOL, $combinations );