# Gift Card GraphQL

## How to install

Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-gift-card-graphql
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## How to use

To start working with Gift Card GraphQL in Magento, you need the following:

- Use Magento 2.3.x. Return your site to developer mode
- Install [chrome extension](https://chrome.google.com/webstore/detail/chromeiql/fkkiamalmpiidkljmicmjfbieiclmeij?hl=en) (currently does not support other browsers)
- Set **GraphQL endpoint** as `http://<magento2-3-server>/graphql` in url box, click **Set endpoint**. (e.g. http://develop.mageplaza.com/graphql/ce232/graphql)
- Mageplaza-supported queries are fully written in the **Description** section of `Query.Blogs`

![](https://i.imgur.com/rjCYdtu.png)

- In addition, the label information is also displayed when using GraphQL to retrieve the information of the Product according to Magento. Supported queries are fully written at `Product.ProductInterface.mp_label_data.LabelRules`


![](https://i.imgur.com/EfVzRxD.png)
