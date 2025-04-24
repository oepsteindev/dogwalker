# dogwalker
A calendar scheduling system for dogwalkers in Symfony and Vue3

This project uses Symfony (latest 2025) and Vue 3, and is a practice project for me to update my framework skills.
This project was developed locally using DDEV
Should you wish to clone and run this project, you may, but please be aware that you will have to:

1. have DDEV installed, cd into the project dir and type ddev start, this should create the entire env for you, provided you have docker installed
2. You can start with a blank DB, but there are fixtures that you can add (otherwise there will be no dogwalkers to search!). You can add those fixtures with Symfony commands:
ddev php bin/console doctrine:fixtures:load
ddev php bin/console doctrine:fixtures:load --append
This should get your DB loaded up with dogwalkers, I suggest changing some of their zip codes to yours, so it makes searching easier.
Happy walking!



![Screenshot 2025-04-24 at 8 27 11 AM](https://github.com/user-attachments/assets/8c219579-4701-4890-95cb-66365c457cfd)
![Screenshot 2025-04-24 at 8 27 24 AM](https://github.com/user-attachments/assets/fd71dba5-2e24-485f-91fb-95540201102c)
![Screenshot 2025-04-24 at 8 27 53 AM](https://github.com/user-attachments/assets/f5f4cdee-15b1-4f29-ad8a-c31591e79778)
![Screenshot 2025-04-24 at 8 28 32 AM](https://github.com/user-attachments/assets/6141464b-d593-4e02-8d66-0e2b0e0d5ff0)
![Screenshot 2025-04-24 at 8 28 49 AM](https://github.com/user-attachments/assets/9a939aa7-2f12-45d4-b0d6-b6a8dd704d94)

