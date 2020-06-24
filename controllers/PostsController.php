<?php

class PostsController extends Controller
{    
    public function __construct()
    {
        $this->postManager = $this->loadModel("PostManager");
        $this->userManager = $this->loadModel('UserManager');
        $this->commentManager = $this->loadModel('CommentManager');
    }

    public function index()
    {  
       $posts = $this->postManager->findAllPublished();

       $popularOne = $this->postManager->findPopular();
       $catstat = $this->postManager->countPostsByCategory();
       $newPost = $this->postManager->findNew();
       
       $this->loadView('posts',['posts'=>$posts, 'popularOne'=>$popularOne, 'categorystats'=>$catstat, 'newPost'=>$newPost]);
    }

    public function list()
    {        
        $posts = $this->postManager->findAll();
        $this->loadView('admin/postsAdmin',['posts'=> $posts]);
    }

    public function show($id)
    {
        $post = $this->postManager->showOneById($id);
        $comments = $this->commentManager->findCommentsByPost($id);
        $this->loadView('show',['post'=>$post, 'comments'=>$comments]);
    }
    
    public function add()
    {
        //Avoid data send by GET method
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             //Pocess form
             
            //Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            //Initialize data posted
            $title=trim(htmlspecialchars($_POST['title']));
            $chapo=trim(htmlspecialchars($_POST['chapo']));
            $category=trim(htmlspecialchars($_POST['category']));
            $content=trim(htmlspecialchars($_POST['content']));
            $postImage=trim(htmlspecialchars($_POST['postImage']));

            //Initialize data
            $data = [
                'title' => $title,
                'chapo' => $chapo,
                'category' => $category,
                'content' => $content,
                'postImage' => $postImage,
                'title_error' => '',
                'chapo_error' => '',
                'category_error' => '',
                'content_error' => '',
                'postImage_error' => ''
            ];
            
            //Validate title
            if (empty($data['title'])) {
                $data['title_error'] = 'Veuiller saisir un titre';
            }
            //Validate chapo
            if (empty($data['chapo'])) {
                $data['chapo_error'] = 'Veuiller saisir un chapô';
            }
            //Validate category
            if (empty($data['category'])) {
                $data['category_error'] = 'Veuiller choisir un category valide';
            }
            
            //Validate content
            if (empty($data['content'])) {
                $data['content_error'] = 'Veuiller saisir un contenu';
            }
                        
            //If all errors are empty
            if (empty($data['title_error']) && empty($data['chapo_error'])
            && empty($data['category_error']) && empty($data['content_error'])) {             
                //Validate
                
                $post = new Post();
                //Assign values to the new user to create
                $post->settitle($title)
                        ->setchapo($chapo)
                        ->setcategory($category)
                        ->setcontent($content)
                        ->setAuthorId($_SESSION['user_id'])
                        ->setPostImage($postImage);
                                        
                //insert into db using manager's create method
                $this->postManager->create($post);
            
                header('Location: '. URL_PATH.'post/list');
            } else {
                //Reload view with errors
                $this->loadView('admin/addPost',$data);
            }
        } else {
            //Initialize data for blank form
            $data = [  
                'title' => '',
                'chapo' => '',
                'category' => '',
                'content' => '',
                'postImage' => '',
                'title_error' => '',
                'chapo_error' => '',
                'category_error' => '',
                'content_error' => '',
                'postImage_error' => ''         
            ];
            //Load view
            $this->loadView('admin/addPost',$data);
        }
    }
    
    public function publish($id)
    {  
        //Get post to publish from db
        $postToPublish = $this->postManager->findById($id);
        //publish into db
        $this->postManager->publishOne($postToPublish);
        $message = "Post publié avec success";
        $posts = $this->postManager->findAll();
        //header('Location: '. URL_PATH.'posts/list',['message' => $message]);
        $this->loadView('admin/postsAdmin',['posts' => $posts, 'message' => $message]);
    }

    public function unPublish($id)
    {
        //Get post to unPublish from db
        $postToUnpublish = $this->postManager->findById($id);
        //publish into db
        $this->postManager->unPublishOne($postToUnpublish);
        $message = "Post retiré avec success.";
        $posts = $this->postManager->findAll();
        //header('Location: '. URL_PATH.'posts/list',['message' => $message]);
        $this->loadView('admin/postsAdmin',['posts' => $posts, 'message' => $message]);
        //header('Location: '. URL_PATH.'posts/list');
    }
    
    public function edit($id)
    {        
        //Avoid data send by GET method
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             //Pocess form
             
            //Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            //Initialize data posted
            $title=trim(htmlspecialchars($_POST['title']));            
            $category=trim(htmlspecialchars($_POST['category']));
            $chapo=trim(htmlspecialchars($_POST['chapo']));
            $content=trim(htmlspecialchars($_POST['content']));
            $postImage=trim(htmlspecialchars($_POST['postImage']));

            //Initialize data
            $data = [
                    'id' => $id,
                    'title' => $title,
                    'category' => $category,
                    'chapo' => $chapo,
                    'content' => $content,
                    'postImage' => $postImage,
                    'title_error' => '',
                    'chapo_error' => '',
                    'category_error' => '',
                    'postImage_error' => ''
                ];
            
            //Validate title
            if (empty($data['title'])) {
                $data['title_error'] = 'Veuiller saisir le titre';
            }
            //Validate chapo
            if (empty($data['chapo'])) {
                $data['chapo_error'] = 'Veuiller saisir un chapo';
            }
            //Validate category
            if (empty($data['category'])) {
                $data['category_error'] = 'Veuiller saisir une catégorie';
            }
            //Validate content
            if (empty($data['content'])) {
                $data['content_error'] = 'Veuiller saisir un contenu';
            } 
            
            //If all errors are empty
            if (empty($data['title_error']) && empty($data['chapo_error'])
            && empty($data['category_error']) && empty($data['content_error'])) {             
                //Validate
                
                //Get post to update from Manager
                $postToUpdate = $this->postManager->findById($id);                      
                //Assign values to the post to edit
                $postToUpdate->settitle($data['title'])
                            ->setchapo($chapo)
                            ->setcategory($category)
                            ->setcontent($content)
                            ->setAuthorId($_SESSION['user_id'])
                            ->setPostImage($postImage);
                                     
                //insert into db
                $this->postManager->update($postToUpdate);                
            
                header('Location: '. URL_PATH.'posts/list');
            } else {
                //Reload view with errors
                $this->loadView('admin/editPost',$data);
            }            
        } else {
            //Get existing post & author from db
            $post = $this->postManager->findById($id);
            $user = $this->userManager->findById($post->getAuthorId());

            //Check for ownership
            if ($user->getId() != $_SESSION['user_id']) {
                //TO BE USED FOR posts
                header('Location: '. URL_PATH.'post/list');    
            }
            //Initialize data for edit form
            $data = [
                'id' => $id,
                'title' => $post->getTitle(),
                'category' => $post->getCategory(),
                'chapo' => $post->getChapo(),
                'content' => $post->getContent(),
                'postImage' => $post->getPostImage(),
                'title_error' => '',
                'category_error' => '',
                'chapo_error' => '',
                'content_error' => '',
                'postImage_error' => ''
            ];
            //Load view
            $this->loadView('admin/editPost',$data);
        }
    }
}