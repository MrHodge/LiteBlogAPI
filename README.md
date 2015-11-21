# LiteBlogAPI
PHP classes for the LiteBlog API. One class for a VenomFramework application and one class for any other application.
### Example Usage
```php
require 'LiteBlogAPI.php'; //In the VenomFramework application use models\LiteBlogAPI instead of require.
$liteBlogAPI = new LiteBlogAPI('https://blog.example.com/api/', 'your-api-key-here');
$posts = $liteBlogAPI->getPosts();
foreach ($posts->payload as $post) {
	echo "<h1> " . $post->title . "</h1>";
	echo $post->content;
	echo "<br><br><br>";
}
```