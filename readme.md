# Ao-Comments

### 1) Installing
````
$ composer require alex-oliveira/ao-comments
````

### 2) Configuring "config/app.php" file
````
'providers' => [
    /*
     * Vendor Service Providers...
     */
    AoComments\ServiceProvider::class,
],
````

### 3) Create "config/ao.php" file
````
return [
    .
    .
    .
    'models' => [
        'users' => App\Models\User::class,
    ],
        
    'tables' => [
        'users' => 'users'
    ]
    .
    .
    .
];
````

### 4) Publish migrations
````
$ php artisan vendor:publish
$ composer dump
````





# Utilization 

## Migration

### Up
````
public function up()
{
    AoComments()->schema()->create('posts');
}
````
the same that
````
public function up()
{    
    Schema::create('ao_comments_x_posts', function (Blueprint $table) {
        $table->integer('post_id')->unsigned();
        $table->foreign('post_id', 'fk_posts_x_ao_comments')->references('id')->on('posts');
        
        $table->bigInteger('comment_id')->unsigned();
        $table->foreign('comment_id', 'fk_ao_comments_x_posts')->references('id')->on('ao_comments_comments');
        
        $table->primary(['post_id', 'comment_id'], 'pk_ao_comments_x_posts');
    });
}
````

### Down
````
public function down()
{
    AoLogs()->schema()->drop('posts');
}
````
the same that
````
public function down()
{    
    Schema::dropIfExists('ao_comments_x_posts');
}
````





## Model
````
namespace App\Models;

use AoComments\Models\Comment;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    /**
     * @return Comment[]|\Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->belongsToMany(Comment::class, AoComments()->schema()->table($this->getTable()));
    }
    
}
````
the same that
````
return $this->belongsToMany(Comment::class, 'ao_comments_x_posts');
````





## Controller
````
namespace App\Http\Controllers\Posts;

use AoComments\Controllers\AoCommentsController;
use App\Models\Post;

class CommentsController extends AoCommentsController
{

    protected $dynamicClass = Post::class;
    
}
````





## Routes
````
Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {

    AoComments()->router()->controller('Posts\CommentsController')->foreign('post_id')->make();
    .
    .
    .
    
});
````

### Checking routes
````
$ php artisan route:list
````