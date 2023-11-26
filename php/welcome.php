<?php
session_start();
include "./db_conn.php";

// Check if the user is logged in
if (!isset($_SESSION['rollNumber'])) {
    // If not logged in, redirect to the login page
    header("Location: ../index.php");
    exit();
}

$rollNumber = $_SESSION['rollNumber'];
$query = "SELECT * FROM profiles WHERE roll_number = ?";
$stmt = $conn->prepare($query);

$stmt->bind_param("i", $rollNumber);
$stmt->execute();
$result = $stmt->get_result();

$userDetails = $result->fetch_assoc();
$name = $userDetails['name'];
// $year = $userDetails['year'];
// $branch = $userDetails['branch'];
// $email = $userDetails['email'];
// $linkedinProfileLink = $userDetails['linkedin_profile_link'];
$company = $userDetails['company'];
$designation = $userDetails['designation'];
$noOfPosts = $userDetails['no_of_posts'];



if (!empty($_POST['content'])) {
    $content = htmlspecialchars($_POST['content']);
    $sql = "INSERT INTO posts (post_id, roll_number, content, likes) VALUES (SHA2(CONCAT(?, NOW()), 256), ?, ?, 0);";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $rollNumber, $rollNumber, $content);
    if (!$stmt->execute()) {
        $error = $stmt->error;
    }
    
    $sql = "UPDATE profiles 
            SET no_of_posts = no_of_posts + 1 
            WHERE roll_number = ?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rollNumber);
    if (!$stmt->execute()) {
        $error = $stmt->error;
    }
    
    $stmt->close();
    header("Location: ./welcome.php");
    exit();
}



// Include any additional logic or fetch more user details as needed

// Now you can display a welcome message or any other content
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="../CSS/style.css" />
    <link rel="stylesheet" type="text/css" href="../CSS/index.css" />
    <link rel="stylesheet" type="text/css" href="../CSS/main.css" />
    <link rel="stylesheet" type="text/css" href="../CSS/profile.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            box-sizing: border-box;
        }
    </style>
</head>

<body class="flex flex-col justify-center items-center">
    <header class="z-99 px-4 w-full xl:max-w-[1600px] lg:max-w-[1060px] h-20 flex justify-between items-center">
        <h1 class="text-black lg:text-4xl text-2xl font-bold">Alumni Portal</h1>
        <nav class="flex justify-between items-center gap-6">
            <div class="flex justify-start items-center gap-2">
                <button onclick="() => {console.log('click');}">
                    <svg class="h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.5 7.063C16.5 10.258 14.57 13 12 13c-2.572 0-4.5-2.742-4.5-5.938C7.5 3.868 9.16 2 12 2s4.5 1.867 4.5 5.063zM4.102 20.142C4.487 20.6 6.145 22 12 22c5.855 0 7.512-1.4 7.898-1.857a.416.416 0 0 0 .09-.317C19.9 18.944 19.106 15 12 15s-7.9 3.944-7.989 4.826a.416.416 0 0 0 .091.317z" fill="#000000"></path>
                        </g>
                    </svg>
                </button>
                <span class="text-lg font-semibold hidden sm:block"><?php echo $name; ?></span>
            </div>
            <svg class="h-8 transition-all duration-150 hover:drop-shadow-lg hover:rotate-180" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <path d="M7 10L12 15L17 10" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </g>
            </svg>
        </nav>
    </header>
    <main class="mt-4 grid xl:grid-cols-4 lg:grid-cols-3 gap-6 max-w-[1600px] grid-col-2 w-full h-screen mx-4 px-4">
        <section id="discover" class="hidden lg:flex flex-col gap-8 items-center">
            <div id="profile" class="w-full drop-shadow-md bg-white relative flex rounded-md flex-col justify-center items-center">
                <div class="w-full h-24 bg-blue-900 rounded-md">

                </div>
                <div class="w-24 h-24 rounded-full bg-black absolute top-12 border border-white border-2 "></div>
                <p class="mt-16 ">
                    <a href="./profile.php">
                        <h1 class="font-bold"><?php echo $name; ?></h1>
                    </a>
                <div class="flex flex-col mt-1 items-center text-sm text-blue-900">
                    <span><?php echo $designation; ?></span>
                    <span>@<?php echo $company; ?></span>
                </div>
                </p>
                <div class="flex text-sm mb-6 justify-between w-full px-8 mt-4">
                    <span class="font-bold">No. of Posts</span>
                    <span><?php echo $noOfPosts; ?></span>
                </div>
            </div>
            <div id="discover-card" class="rounded-md drop-shadow-md bg-white w-full">
                <div class="flex justify-center items-center w-full h-10 bg-blue-900 rounded-md">
                    <h3 class="font-bold text-white">Discover More</h3>
                </div>
                <div class="w-full h-40">

                </div>
            </div>
        </section>

        <!-- POSTS -->
        <section id="posts" class="col-span-2">

            <?php
            $sql = "SELECT posts.*, profiles.name, profiles.company, profiles.designation
            FROM posts
            INNER JOIN profiles ON posts.roll_number = profiles.roll_number
            ORDER BY posts.time_stamp DESC
            LIMIT 5;";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($post = $result->fetch_assoc()) {
                    echo <<<HTML
                        <div id="post-card" class="bg-white w-full rounded-lg drop-shadow-lg flex flex-col">
                            <div class="p-6 flex gap-2 justify-start items-center relative">
                                <svg class='w-12' viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path opacity="0.4" d="M12.1207 12.78C12.0507 12.77 11.9607 12.77 11.8807 12.78C10.1207 12.72 8.7207 11.28 8.7207 9.50998C8.7207 7.69998 10.1807 6.22998 12.0007 6.22998C13.8107 6.22998 15.2807 7.69998 15.2807 9.50998C15.2707 11.28 13.8807 12.72 12.1207 12.78Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path opacity="0.34" d="M18.7398 19.3801C16.9598 21.0101 14.5998 22.0001 11.9998 22.0001C9.39977 22.0001 7.03977 21.0101 5.25977 19.3801C5.35977 18.4401 5.95977 17.5201 7.02977 16.8001C9.76977 14.9801 14.2498 14.9801 16.9698 16.8001C18.0398 17.5201 18.6398 18.4401 18.7398 19.3801Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </g>
                                </svg>
                                <div class="flex flex-col text-xs leading-3">
                                    <span class="text-base font-extrabold">{$post['name']}</span>
                                    <span>{$post['designation']}</span>
                                    <span>@{$post['company']}</span>
                                </div>
                                <button class="w-8 h-8 flex justify-center items-center rounded-full hover:bg-lightGrey/30 right-0 mr-6 absolute hover:drop-shadow-sm transition-all hover:-translate-y-1">
                                    <svg class="w-5" fill="#000000" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path d="M28.106 19.944h-0.85c-0.069-0.019-0.131-0.050-0.2-0.063-1.788-0.275-3.2-1.762-3.319-3.506-0.137-1.95 0.975-3.6 2.787-4.137 0.238-0.069 0.488-0.119 0.731-0.181h0.85c0.056 0.019 0.106 0.050 0.169 0.056 1.65 0.269 2.906 1.456 3.262 3.081 0.025 0.125 0.063 0.25 0.094 0.375v0.85c-0.019 0.056-0.050 0.113-0.056 0.169-0.262 1.625-1.419 2.863-3.025 3.238-0.156 0.038-0.3 0.081-0.444 0.119zM4.081 12.056l0.85 0c0.069 0.019 0.131 0.050 0.2 0.056 1.8 0.281 3.206 1.775 3.319 3.537 0.125 1.944-1 3.588-2.819 4.119-0.231 0.069-0.469 0.119-0.7 0.175h-0.85c-0.056-0.019-0.106-0.050-0.162-0.063-1.625-0.3-2.688-1.244-3.194-2.819-0.069-0.206-0.106-0.425-0.162-0.637v-0.85c0.019-0.056 0.050-0.113 0.056-0.169 0.269-1.631 1.419-2.863 3.025-3.238 0.15-0.037 0.294-0.075 0.437-0.113zM15.669 12.056h0.85c0.069 0.019 0.131 0.050 0.2 0.063 1.794 0.281 3.238 1.831 3.313 3.581 0.087 1.969-1.1 3.637-2.931 4.106-0.194 0.050-0.387 0.094-0.581 0.137h-0.85c-0.069-0.019-0.131-0.050-0.2-0.063-1.794-0.275-3.238-1.831-3.319-3.581-0.094-1.969 1.1-3.637 2.931-4.106 0.2-0.050 0.394-0.094 0.588-0.137z"></path>
                                        </g>
                                    </svg>
                                </button>
                            </div>
                            <!-- <div class="px-6 mb-4 w-full flex justify-center items-center">
                                <img class="w-full rounded-lg drop-shadow-sm" src="" alt="">
                            </div> -->
                            <p class="p-2 px-6 text-justify text-sm">{$post['content']}</p>
                            <div class="text-sm px-6 p-2 pb-6 flex justify-between">
                                <div class="flex gap-2 justify-start items-center">
                                    <button id="like" class="like-count">
                                        <svg class="w-5 transition-all hover:drop-shadow-lg hover:-translate-y-1" fill="#000000" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <path d="M1637.176 1129.412h-112.94v112.94c62.23 0 112.94 50.599 112.94 112.942 0 62.344-50.71 112.941-112.94 112.941h-112.942v112.941c62.23 0 112.941 50.598 112.941 112.942 0 62.343-50.71 112.94-112.94 112.94h-960c-155.634 0-282.354-126.606-282.354-282.352V903.529h106.617c140.16 0 274.334-57.6 368.3-157.778C778.486 602.089 937.28 379.256 957.385 112.94h36.367c50.484 0 98.033 22.363 130.334 61.44 32.64 39.53 45.854 91.144 36.14 141.515-22.7 118.589-60.197 236.048-111.246 349.102-23.83 52.517-19.313 112.602 11.746 160.94 31.397 48.566 84.706 77.591 142.644 77.591h433.807c62.231 0 112.942 50.598 112.942 112.942 0 62.343-50.71 112.94-112.942 112.94m225.883-112.94c0-124.575-101.308-225.883-225.883-225.883H1203.37c-19.651 0-37.044-9.374-47.66-25.863-10.391-16.15-11.86-35.577-3.84-53.196 54.663-121.073 94.87-247.115 119.378-374.513 15.925-83.576-5.873-169.072-60.085-234.578C1157.29 37.384 1078.005 0 993.751 0H846.588v56.47c0 254.457-155.068 473.224-285.063 612.029-72.734 77.477-176.98 122.09-285.967 122.09H56v734.117C56 1742.682 233.318 1920 451.294 1920h960c124.574 0 225.882-101.308 225.882-225.882 0-46.42-14.117-89.676-38.174-125.59 87.869-30.947 151.116-114.862 151.116-213.234 0-46.419-14.118-89.675-38.174-125.59 87.868-30.946 151.115-114.862 151.115-213.233" fill-rule="evenodd"></path>
                                            </g>
                                        </svg>
                                    </button>
                                    <span class="font-bold">{$post['likes']} Likes</span>
                                </div>
                                <button>
                                    <svg class="w-6 hover:drop-shadow-lg transition-all hover:-translate-y-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path d="M9 12C9 13.3807 7.88071 14.5 6.5 14.5C5.11929 14.5 4 13.3807 4 12C4 10.6193 5.11929 9.5 6.5 9.5C7.88071 9.5 9 10.6193 9 12Z" stroke="#000000" stroke-width="1.5"></path>
                                            <path d="M14 6.5L9 10" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
                                            <path d="M14 17.5L9 14" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
                                            <path d="M19 18.5C19 19.8807 17.8807 21 16.5 21C15.1193 21 14 19.8807 14 18.5C14 17.1193 15.1193 16 16.5 16C17.8807 16 19 17.1193 19 18.5Z" stroke="#000000" stroke-width="1.5"></path>
                                            <path d="M19 5.5C19 6.88071 17.8807 8 16.5 8C15.1193 8 14 6.88071 14 5.5C14 4.11929 15.1193 3 16.5 3C17.8807 3 19 4.11929 19 5.5Z" stroke="#000000" stroke-width="1.5"></path>
                                        </g>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <hr class="m-2" style="color:transparent;">
                    HTML;
                }
            } else {
                echo '<p> NO POSTS FOUND! </p>';
            }
            $content = '';
            ?>


        </section>

        <!-- CREATE POSTS -->
        <section id="create-post" class="hidden xl:block">
            <div id="" class="w-full drop-shadow-md bg-white relative flex rounded-md flex-col justify-center items-center">
                <div class="flex justify-center items-center w-full h-10 bg-blue-900 rounded-md">
                    <h3 class="font-bold text-white">Create Post</h3>
                </div>
                <!-- <div class="m-4 p-2 h-32 bg-gray-100 rounded-md border border-black">
                </div> -->

                <!-- CREATE POST FORM -->
                <form action="./welcome.php" method="post">
                    <textarea name="content" placeholder="Start typing..." class="m-4 mt-2 p-2 bg-gray-100 rounded-md h-32 text-gray-600 " id="postContent" cols="30" rows=""></textarea>

                    <div class="m-4 mb-0 mt-0 w-full px-4 flex justify-end gap-2">
                        <button class="flex justify-center items-center h-8 w-8 hover:bg-gray-200/50 rounded-full">
                            <svg class="h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path d="M14 7H16C18.7614 7 21 9.23858 21 12C21 14.7614 18.7614 17 16 17H14M10 7H8C5.23858 7 3 9.23858 3 12C3 14.7614 5.23858 17 8 17H10M8 12H16" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </g>
                            </svg>
                        </button>
                        <button class="flex justify-center items-center h-8 w-8 hover:bg-gray-200/50 rounded-full">
                            <svg class="h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <rect x="0" fill="none" width="24" height="24"></rect>
                                    <g>
                                        <path d="M23 4v2h-3v3h-2V6h-3V4h3V1h2v3h3zm-8.5 7c.828 0 1.5-.672 1.5-1.5S15.328 8 14.5 8 13 8.672 13 9.5s.672 1.5 1.5 1.5zm3.5 3.234l-.513-.57c-.794-.885-2.18-.885-2.976 0l-.655.73L9 9l-3 3.333V6h7V4H6c-1.105 0-2 .895-2 2v12c0 1.105.895 2 2 2h12c1.105 0 2-.895 2-2v-7h-2v3.234z"></path>
                                    </g>
                                </g>
                            </svg>
                        </button>
                    </div>
                    <div class="p-2 w-full mb-2">
                        <button type="submit" class="bg-red-500 px-4 py-2 rounded-lg w-full font-bold text-white ">
                            Post
                        </button>
                    </div>
                </form>

            </div>
            <footer class="flex flex-col justify-center items-center gap-4 p-4">
                <div class="flex flex-col justify-center items-center text-sm gap-1">
                    <span>+91 94144 03565 (Phone)</span>
                    <span>0172-2750872 (Fax)</span>
                    <a href="https://www.ccet.ac.in/tnp/index.html" target="_blank" rel="noopener noreferrer">www.ccet.ac.in/tnp/index.html</a>
                    <!-- <a href="https://tpo@ccet.ac.in" target="_blank" rel="noopener noreferrer">tpo@ccet.ac.in</a> -->
                    <a href="https://www.ccet.ac.in" target="_blank" rel="noopener noreferrer">www.ccet.ac.in</a>
                </div>
                <div class="flex text-blue-900  flex-col justify-center items-center">
                    <span class="">TPC @ CCET © 2023 </span>
                    <span class="text-xs">All Rights Reserved</span>
                </div>
            </footer>
        </section>


    </main>
</body>

</html>