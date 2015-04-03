<?PHP

spl_autoload_register(function ($class) {
	$classPath = str_replace('\\', '/', $class);
	include __DIR__.'/src/'.$classPath.'.php';
});