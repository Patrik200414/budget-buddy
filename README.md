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
![homePage]
![registration]
![profilePage]
![productUpload]
![imageSelection]

### Built With


* [![Spring Boot][Spring Boot]][Spring-boot-url]
* [![Java][Java]][Java-url]
* [![React][React]][React-url]
* [![PostgreSQL][PostgreSQL]][PostgreSQL-url]
<!-- * [![Docker][Docker]][Docker-url] -->

<p align="right">(<a href="#readme-top">back to top</a>)</p>



## Getting Started

Clone the repository to your machine: https://github.com/Patrik200414/Danube

I assume you have Maven and JRE with PostgreSQL or Docker downloaded to your machine.
If not here are some links where you can download from :
- Maven: https://maven.apache.org/download.cgi
- JRE: https://www.java.com/en/download/manual.jsp
- PostgreSQL: https://www.postgresql.org/download/
- Node JS: https://nodejs.org/en/download
<!-- - Docker: https://www.docker.com/products/docker-desktop/ -->

### Prerequisites
If you would like to try out the payment system, please create a test account on Stripe
- https://stripe.com/en-hu

#### Backend: 
For manual setup add the following environment variables (If you use a shell to set the environment you should insert the values between quotes ("VALUE")):
  - `DB_PASSWORD`: [Your database password]
  - `DB_URL`: [Your database URL]
  - `DB_USERNAME`: [Your database username]
  - `FILE_LOG_PATH_ERROR`: [The file path where to create the log file, e.g: C:\Users\example\logs\error.txt]
  - `JWT_SECRET`: [Your JWT secret key]
  - `JWT_EXPIRATION`: [JWT token expiration time in millisecond]
  - `JWT_VERIFICATION_TIME_IN_MINUTES`: [If the user want to modify there data the server will ask to authorize after verification time expires, the recomandation is 5 minutes]
  - `PAYMENT_REDIRECTION_PAGE`: [The location of the website where to redirect after successfull payment]
  - `PAYMENT_SECRET`: [Your Stripe secret key (If you try this project in your local environment you can set this value to `http://localhost:5173`)]


<!-- For Docker setup add the following environment variables:
  - `DB_NAME`: [Your database name]
  - `DB_USER`: [Your database username]
  - `DB_PASSWORD`: [Your database password]
  - `DDL_AUTO`: [update, create-drop]
  - `JWT_EXPIRATION`: [JWT token expiration time in millisecond]
  - `JWT_SECRET`: [Your JWT key] -->


### Run Project

#### Manual setup
- Backend:
  - Navigate to danube/target
  - Run `java -jar danube-0.0.1-SNAPSHOT.jar`
 
- Frontend:
  - Navigate to client/
  - Run `npm install`
  - Create .env file like in the .env.sample
  - Run `npm run dev`

 - You can check out `http://localhost:5173/` to see the project!

<!-- - Manual setup:
  1. Run `mvn clean package` command
  2. To run project execute `java -jar target/SolarWatchApplication-0.0.1-SNAPSHOT.jar` command

- Docker setup:
  1. Run `docker-compose up` command -->

<p align="right">(<a href="#readme-top">back to top</a>)</p>





### Contact
[![LinkedIn][linkedin-shield]][linkedin-url]
[![Email][email-shield]][email-address]

[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://www.linkedin.com/in/patrik-martin2004/
[email-shield]: https://img.shields.io/badge/Email-address?style=for-the-badge&logo=gmail&color=555
[email-address]: mailto:martinbpatrik@gmail.com
[Spring Boot]: https://img.shields.io/badge/Spring-Boot?style=flat&logo=spring&logoColor=%23FFFFFF
[Spring-boot-url]: https://spring.io/
[Java]: https://img.shields.io/badge/Java-21?logo=openjdk&color=%23FF0000
[Java-url]: https://www.java.com/en/
[PostgreSQL]: https://img.shields.io/badge/Postgres-SQL?style=flat&logo=postgresql&logoColor=%23FFFFFF&color=%23008bb9
[PostgreSQL-url]: https://www.postgresql.org/docs/
[Docker]: https://img.shields.io/badge/Docker-a?style=flat&logo=docker&logoColor=%23FFFFFF&color=%23384d54
[Docker-url]: https://docs.docker.com/
[React]: https://img.shields.io/badge/React-logo?style=flat&logo=react&color=%235A5A5A
[React-url]: https://react.dev/

[homePage]: ./product_images/homePage.png
[registration]: ./product_images/registration.png
[profilePage]: ./product_images/profilepage.png
[productUpload]: ./product_images/productupload.png
[imageSelection]: ./product_images/imageSelection.png

