# Gift Card GraphQL

Mageplaza Gift Card Extension supports getting and pushing data on the website with GraphQl.

## How to install

Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-gift-card-graphql
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## How to use


To start working with **Gift Card GraphQl** in Magento, you need to:

- Use Magento 2.3.x. Return your site to developer mode
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

