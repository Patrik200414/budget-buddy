<div align="center" name="readme-top">
  <h3 align="center">Budget Buddy</h3>
</div>


<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="project-images">Project images</a></li>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <!-- <li><a href="#run-project">Run Project</a></li> -->
      </ul>
    </li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>


## About The Project

Budget Buddy is a personal finance tracking application. You can create different types of accounts, make transactions, also you can download monthly reports.
(This is not finished yet and under development)

<p align="right">(<a href="#readme-top">back to top</a>)</p>


## Project Images
![dashboard]
![profile]

### Built With


* [![Laravel][Laravel]][Laravel-url]
* [![PHP][PHP]][PHP-url]
* [![Vue][Vue]][Vue-url]
* [![Mysql][Mysql]][Mysql-url]
<!-- * [![Docker][Docker]][Docker-url] -->

<p align="right">(<a href="#readme-top">back to top</a>)</p>



## Getting Started

Clone the repository to your machine: https://github.com/Patrik200414/budget-buddy

I assume you have PHP, Composer, Node.js, MySql and Docker downloaded to your machine.
If not here are some links where you can download from :
- PHP: https://www.php.net/downloads.php
- Composer: https://getcomposer.org/download/
- MySQL: https://dev.mysql.com/downloads/installer/
- Node JS: https://nodejs.org/en/download
- Docker: https://www.docker.com/products/docker-desktop/

### Prerequisites
- Install Laravel to Composer: https://laravel.com/docs/11.x/installation
- Download Mailpit Docker image: https://hub.docker.com/r/axllent/mailpit

#### Backend setup: 
For manual setup create a .env file in the backend folder. It should look like the .env.sample file.
Please change these values in the .env file:
  - DB_CONNECTION=mysql
  - DB_HOST=127.0.0.1 [default MySQL host address]
  - DB_PORT=3306 [default MySQL port]
  - DB_DATABASE=[database name]
  - DB_USERNAME=[database user name]
  - DB_PASSWORD=[database password]

  - MAIL_MAILER=smtp
  - MAIL_HOST=localhost
  - MAIL_PORT=1025

  - FRONT_END_URL="http://localhost:5173" [If you run this project in your loacal environment keep it this way, this is link where should the email redirect you]
  - AUTH_KEY_NAME=[Your authentication keyname, which will be referenced from the frontend, this should be the same as `VITE_AUTH_KEY_NAME` on the frontend folder]

### Frontend setup:
For mainual setup create a .env like in the .env.sample file.
Please change these values:
  - VITE_AUTH_KEY_NAME=[Your authentication keyname, this should be the same as the AUTH_KEY_NAME in the backend folder]

### Run Project

#### Manual setup
- Run Mailpit image with this command:
  - docker run -p 8025:8025 -p 1025:1025 [mailpit docker image id]
  
- Backend:
  - Navigate to ./backend
  - Install dependencies: `php artisan install`
  - Run migrations: `php artisan migrate`
  - To populate the database with the necessary account informations run: `php artisan app:integrate-transaction-categories`
  - Run the server: `php artisan serve`
 
- Frontend:
  - Navigate to ./frontend
  - Run `npm install`
  - Run `npm run dev`

 - You can check out `http://localhost:5173/` to see the project!


<p align="right">(<a href="#readme-top">back to top</a>)</p>





### Contact
[![LinkedIn][linkedin-shield]][linkedin-url]
[![Email][email-shield]][email-address]

[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://www.linkedin.com/in/patrik-martin2004/
[email-shield]: https://img.shields.io/badge/Email-address?style=for-the-badge&logo=gmail&color=555
[email-address]: mailto:martinbpatrik@gmail.com
[Laravel]: https://img.shields.io/badge/Laravel-framework?logo=laravel&logoColor=white&color=red
[Laravel-url]: https://laravel.com/
[PHP]: https://img.shields.io/badge/PHP-language?logo=PHP&logoColor=white&color=purple
[PHP-url]: https://www.php.net/
[Mysql]: https://img.shields.io/badge/MySQL-database?logo=MySQL&logoColor=white&color=orange
[Mysql-url]: https://www.mysql.com/
[Vue]: https://img.shields.io/badge/Vue-frontend?logo=Vue.js&logoColor=white
[Vue-url]: https://vuejs.org/

[dashboard]: ./project_images/dashboard.png
[profile]: ./project_images/profile.png

