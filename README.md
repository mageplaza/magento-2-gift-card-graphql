# Magento 2 Gift Card GraphQL / PWA (FREE)

[Mageplaza Gift Card for Magento 2](https://www.mageplaza.com/magento-2-gift-card-extension/) helps online stores improve customer experience by giving customers gift cards and allowing them to send to others as gifts. 

You can create unlimited gift cards and give away to your customers or use them for product promotion. An outstanding feature of this extension is that customers can also customize their gift cards by adding images and messages to the cards. This customization option allows customers to turn their gift cards into a more personalized and meaningful gift that might be given to their friends or beloved ones. 

If you want to make your gift cards look more beautiful and attractive to customers, you can change the gift card design right from the admin backend with a simple drag and drop configuration. You can change the positions and sizes of the blocks on the gift card and add eye-catching visual elements to make your digital gift card beautiful, like a real one. 

Gift cards can be sent to customers via SMS, email, or online messenger apps, such as Facebook Messenger, Whatsapp, Viber, or Tango. How the gift cards are sent fully depends on the customers’ choices. Even it can be printed offline and sent to customers via post office. In case customers want to send their gift cards to someone, they can leave messages on the gift cards and get SMS notifications about the delivery status of the cards. 

When you give customers gift cards as discounts or coupon codes, customers can use these gift cards at the local store to purchase products. 

With the flexibility to customize the gift card templates, Mageplaza Gift Card extension is a great tool to increase customer satisfaction and sales for online stores, especially during the holidays. 

Notably, **Magento 2 Gift Card GraphQL is now available in Mageplaza Gift Card extension that adds GraphQL features.** This supports PWA compatibility. The extension now supports getting and pushing data on the website with GraphQl.

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

To start working with **Gift Card GraphQl** in Magento, you need to:

- Use Magento 2.3.x or higher. Return your site to developer mode
- Install [chrome extension](https://chrome.google.com/webstore/detail/chromeiql/fkkiamalmpiidkljmicmjfbieiclmeij?hl=en) (currently does not support other browsers)
- Set **GraphQL endpoint** as `http://<magento2-3-server>/graphql` in url box, click **Set endpoint**. (e.g. http://develop.mageplaza.com/graphql/ce232/graphql)


The module currently supports the following 6 types of queries and 10 types of mutations:

- Query **mpGiftCode**: Support lookup Gift Code information by Gift Code ID.

![](https://i.imgur.com/WTytIzV.png)

- Query **mpGiftCodeList**: Support to look up Gift Code information according to certain conditions fields.

![](https://i.imgur.com/pmDfbAP.png)
![](https://i.imgur.com/oK0QOX6.png)

- Query **mpGiftPool**: Support searching for Gift Pool information by Gift Pool ID.

![](https://i.imgur.com/FXjSGQ4.png)

- Query **mpGiftPoolList**: Support searching for Gift Pool information according to certain conditional fields.

![](https://i.imgur.com/JMklFlG.png)

- Query **mpGiftTemplate**: Support for looking up Template information by Gift Template ID.

![](https://i.imgur.com/DZYohl6.png)

- Query **mpGiftTemplateList**: Support searching for Gift Pool information according to certain conditional fields.

![](https://i.imgur.com/lYi1aXt.png)

- Mutation **mpGiftCodeSave**: Create/edit Gift Code. To edit an existing Gift Code, just input the value for the `giftcard_id` field corresponding to the gift code you want to edit.

![](https://i.imgur.com/4TLFN9C.png)

- Mutation **mpGiftCodeDelete**: Delete the Gift Code according to the corresponding Gift Code ID.

![](https://i.imgur.com/gn30Wy9.png)

- Mutation **mpGiftPoolSave**: Create new/edit Gift Pool. To edit an existing Gift Pool, just enter the value for the `pool_id` field corresponding to the gift pool you want to edit.

![](https://i.imgur.com/CyOsMCo.png)

- Mutation **mpGiftPoolDelete**: Delete Gift Pool by Gift Pool ID.

![](https://i.imgur.com/LsOTYEA.png)

- Mutation **mpGiftPoolGenerate**: Generate Gift Code for Gift Pool with corresponding ID.

![](https://i.imgur.com/V7da98h.png)

- Mutation **mpGiftTemplateSave**: Create/edit Gift Template. To edit an existing Gift Template, just input the value for the `template_id` field corresponding to the template you want to edit.

![](https://i.imgur.com/rI1d53X.png)

- Mutation **mpGiftTemplateDelete**: Delete Gift Template according to Gift Template ID.

![](https://i.imgur.com/lDCqvJL.png)

- Mutation **mpGiftCardRedeem**: Redeem Gift Code for customer with corresponding customerId.

![](https://i.imgur.com/giqmWDT.png)

- Mutation **mpGiftCardSetCode**: Apply Gift Code to cart according to the corresponding cartId.

![](https://i.imgur.com/Hd3PSaW.png)

- Mutation **mpGiftCardRemoveCode**: Cancel Gift Code is being applied to the cart according to the corresponding cartId.

![](https://i.imgur.com/ig0rnKr.png)

- Mutation **mpGiftCardSetCredit**: Apply Gift Credit to cart according to cartId and corresponding amount.

![](https://i.imgur.com/Aqesczf.png)

## 3. Devdocs
- [Magento 2 Gift Card API & examples](https://documenter.getpostman.com/view/10589000/SzYXWeVY?version=latest)
- [Magento 2 Gift Card GraphQL & examples](https://documenter.getpostman.com/view/10589000/TVK5bgJK)

Click on Run in Postman to add these collections to your workspace quickly.

![Magento 2 blog graphql pwa](https://i.imgur.com/lhsXlUR.gif)

## 4. Contribute to this module
Feel free to **Fork** and contribute to this module. 

You can create a pull request, and we will consider to merge your changes in the main branch. 

## 5. Get support
- Feel free to [contact us](https://www.mageplaza.com/contact.html) if you have any question. 
- If you find this post helpful, please give it a **Star** ![star](https://i.imgur.com/S8e0ctO.png)

