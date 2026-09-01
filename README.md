# Line Verified Purchase for Adobe Commerce / Magento 2

Módulo `line/module-verified-purchase` (`Line_VerifiedPurchase`): flujo de verificación
antifraude para el gateway de pago de Line.

## Magento Support
>= 2.4.6-sp3

## Dependencia de Line_Payment

Este módulo **no funciona por sí solo**. Es una extensión de
[`line/module-payment`](https://github.com/hypevar/line-adobe-commerce)
(`Line_Payment`) y se engancha a él en cinco puntos:

* `etc/di.xml` inyecta `Line\VerifiedPurchase\Gateway\Request\VerifiedBuilder` en
  `Line\Payment\Gateway\Request\IntegrationPool`, el punto de extensión que Payment expone
  para que módulos externos agreguen campos al request del gateway.
* `etc/di.xml` declara un plugin `afterBuild` sobre
  `Line\Payment\Gateway\Request\DetailsDataBuilder`.
* `etc/events.xml` observa el evento `line_payment_data_converter_before`, despachado por
  `Line\Payment\Model\Client\DataConverter`.
* `Line\Payment\Model\GetTransactionIdentifierAction` genera el `IdentificadorCliente` que
  ambos módulos comparten por orden. Es estado compartido en el `additional_information`
  del pago: los dos módulos deben leer el mismo valor. Desde Payment 0.5.0 `generate()` recibe
  el `InfoInterface` del pago (antes, la orden) y devuelve el valor ya almacenado en llamadas
  posteriores, así que el orden en que corren los builders no altera el identificador.
* `Line\Payment\Model\Checkout\SensitiveDataRegistry` es la única fuente del PAN.
  `VerificationManager::isPaymentCandidateForVerification()` lo lee de ahí para calcular el
  placeholder de la tarjeta. Hasta Payment 0.4.x el PAN venía en `additional_information`;
  0.5.0 lo sacó de ahí y lo dejó únicamente en este registro, que vive solo durante el request.

Acoplamientos de solo lectura (constantes e interfaces, sin comportamiento):
`Api\Request\{AttributeInterface, BuilderInterface}`, `Api\Response\{GatewayAttributeInterface,
StatusInterface, ErrorCodeInterface}`, `Api\Data\OrderStatusInterface`, `Gateway\DataReader`.

Además, `Model\Client\Connector` toma la configuración SSL de cURL
(`getApiSslIsActive()` / `getApiSslVersion()`) de `Line\Payment\Api\Data\ConfigInterface`,
es decir de la sección de admin `payment/linepayment/api_ssl_*`. Este módulo no tiene
configuración SSL propia.

La dependencia es unidireccional: `Line_Payment` no referencia a `Line_VerifiedPurchase`.

## Configuración

Sección de admin: `Stores > Configuration > Sales > Payment Methods > Verified Purchase (by Line)`
(paths `payment/verified_purchase/*`).

## Tabla

`verified_purchase_customer_order`, con FK a `sales_order` y `customer_entity`.

## Changelog
* 0.5.0:
    - **`VerificationManager` lee el PAN desde `Line\Payment\Model\Checkout\SensitiveDataRegistry`.**
      Payment 0.5.0 sacó `CREDIT_CARD_NUMBER` de `additional_information`; el módulo seguía
      leyendo esa clave, lo que abortaba `VerifiedBuilder` en silencio y hacía que nunca se
      creara una verificación.
    - **`BeforeDataConverter` ya no deja escapar ninguna excepción.** El observer corre después
      de que el gateway respondió, así que cualquier error que se propague destruye la orden y
      deja el cobro sin nada que lo concilie — que es lo que pasó con la tabla
      `verified_purchase_customer_order` ausente. Ambos `catch` capturan `\Throwable` y loguean.
    - suite de tests unitarios (`Test/Unit`, `phpunit.xml.dist`) cubriendo los dos casos.
    - requiere `line/module-payment: ^0.5`. Adaptado al cambio de firma de
      `GetTransactionIdentifierAction::generate()`, que en Payment 0.5.0 pasó a recibir el
      pago en vez de la orden. Con `~0.4` el módulo instalaba Payment 0.5.0 y fallaba al
      autorizar.
    - se refuerza el transporte del gateway: la verificación del certificado pasa a estar
      activa (`CURLOPT_SSL_VERIFYPEER`, `CURLOPT_SSL_VERIFYHOST`), igual que en Payment 0.5.0.
      El módulo comparte el switch SSL de Payment, pero hasta ahora no compartía su refuerzo.
    - las credenciales se desencriptan antes de usarse. Se guardan con backend `Encrypted`,
      pero se enviaban tal cual en la cabecera `Authorization`.
    - el Connector deja de propagar excepciones de red: devuelve una verificación rechazada
      con mensaje para el cliente. Antes, `catch (Exception)` resolvía a
      `Line\VerifiedPurchase\Model\Client\Exception` — una clase que nadie lanza — así que la
      excepción escapaba del checkout.
    - se registran las respuestas no-JSON y los códigos HTTP fuera de 2xx.
    - `DataConverter` tolera respuestas incompletas del gateway en vez de fallar por índice
      inexistente.
    - `ValidateAction::validate()` ya no puede devolver una variable sin asignar; devuelve
      `null` cuando el intento nunca llegó al servicio, y el controller lo contempla.

* 0.4.0:
    - módulo separado a su propio repositorio (antes convivía con `Line_Payment` en
      `line-adobe-commerce`). Namespaces y paths de configuración sin cambios.

* 0.3.3:
    - fix production/sandbox configuration methods

* 0.3.2:
    - add Production default url into configuration
    - make restorable a few configuration options

* 0.3.1: Order Pending Status now updated to allow selections from PROCESSING states


## Update through composer
```
composer require line/module-payment line/module-verified-purchase
```
