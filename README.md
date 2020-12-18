# Magento 2 Gift Card GraphQL / PWA (FREE)

**Magento 2 Gift Card GraphQL is now available in Mageplaza Gift Card extension that adds GraphQL features.** This supports PWA compatibility. The extension now supports getting and pushing data on the website with GraphQL.

[Mageplaza Gift Card for Magento 2](https://www.mageplaza.com/magento-2-gift-card-extension/) helps online stores improve customer experience by giving customers gift cards and allowing them to send to others as gifts. 

You can create unlimited gift cards and give away to your customers or use them for product promotion. An outstanding feature of this extension is that customers can also customize their gift cards by adding images and messages to the cards. This customization option allows customers to turn their gift cards into a more personalized and meaningful gift that might be given to their friends or beloved ones. 

If you want to make your gift cards look more beautiful and attractive to customers, you can change the gift card design right from the admin backend with a simple drag and drop configuration. You can change the positions and sizes of the blocks on the gift card and add eye-catching visual elements to make your digital gift card beautiful, like a real one. 

Gift cards can be sent to customers via SMS, email, or online messenger apps, such as Facebook Messenger, Whatsapp, Viber, or Tango. How the gift cards are sent fully depends on the customers’ choices. Even it can be printed offline and sent to customers via post office. In case customers want to send their gift cards to someone, they can leave messages on the gift cards and get SMS notifications about the delivery status of the cards. 

When you give customers gift cards as discounts or coupon codes, customers can use these gift cards at the local store to purchase products. 

With the flexibility to customize the gift card templates, Mageplaza Gift Card extension is a great tool to increase customer satisfaction and sales for online stores, especially during the holidays. 

## 1. How to install

Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-gift-card-graphql
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

**Note:**
Magento 2 Gift Card GraphQL requires installing [Mageplaza Gift Card](https://www.mageplaza.com/magento-2-gift-card-extension/) in your Magento installation.

## 2. How to use

To perform GraphQL queries in Magento, please do the following requirements:

- Use Magento 2.3.x or higher. Set your site to [developer mode](https://www.mageplaza.com/devdocs/enable-disable-developer-mode-magento-2.html).
- Set GraphQL endpoint as `http://<magento2-server>/graphql` in url box, click **Set endpoint**. 
(e.g. `http://dev.site.com/graphql`)
- To view the queries that the **Mageplaza Gift Card GraphQL** extension supports, you can look in `Docs > Query` in the right corner

## 3. Devdocs

- [Gift Card API & examples](https://documenter.getpostman.com/view/10589000/SzYXWeVY)
- [Gift Card GraphQL & examples](https://documenter.getpostman.com/view/10589000/TVK5bgJK#44580a6d-fdf4-4e4c-80ae-5245609dee7c)


## 4. Contribute to this module

Feel free to **Fork** and contribute to this module and create a pull request so we will merge your changes main branch.

## 5. Get Support

- Feel free to [contact us](https://www.mageplaza.com/contact.html) if you have any further questions.
- Like this project, Give us a **Star** ![star](https://i.imgur.com/S8e0ctO.png)
