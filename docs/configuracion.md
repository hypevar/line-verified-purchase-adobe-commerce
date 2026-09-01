# Configuración de Line Verified Purchase para Magento

Esta guía describe la configuración operativa de `line/module-verified-purchase`
(`Line_VerifiedPurchase`): verificación antifraude mediante código.

Requiere `line/module-payment` (`Line_Payment`) instalado y habilitado; su configuración se
documenta en [line-adobe-commerce](https://github.com/hypevar/line-adobe-commerce)
(`docs/configuracion.md`).

## Acceso a la configuración

`Stores > Configuration > Sales > Payment Methods > Verified Purchase (by Line)`


### Parámetros generales y credenciales

- **Status** habilita o deshabilita el flujo de verificación.
- **Mode** permite seleccionar `Sandbox` o `Production`.
- Cargar la **Production API Key** provista por Line Payments.
- Mantener la URL predeterminada (`https://ccs.4fsoluciones.com.ar/linerestapi/api`) salvo indicación técnica.

### Verificación e intentos

| Campo | Configuración |
| --- | --- |
| **Verification Mode** | Mantener `inmediate mode` salvo indicación del equipo técnico. |
| **Credit Card Summary Description** | Texto que verá el cliente en el resumen de su tarjeta. |
| **Time Unit** | `Day`, `Hour` o `Minute`. |
| **Time Amount** | Valor recomendado: `120`. |
| **Max Tries** | Cantidad máxima de intentos; valor recomendado: `3`. |

### Estados y notificaciones

Configurar de forma coherente los tres estados del proceso:

- **Order Status Process Start**: estado inicial, por ejemplo *En espera de verificación*.
- **Order Status Process Failed**: estado ante un fallo, por ejemplo *Operación sospechosa*.
- **Order Status Process Complete**: estado final exitoso, por ejemplo *Pago aprobado*.

Seleccionar también el **Email Sender** que se usará para las notificaciones. Mantener **Developer Debug** deshabilitado salvo durante diagnósticos puntuales; sus logs se escriben en `var/log/line-payment.log`.

## Recomendaciones operativas

- Usar `Production` únicamente en el entorno productivo.
- No cambiar endpoints ni parámetros técnicos sin indicación de Line Payments.
- Mantener el debug deshabilitado fuera de una investigación puntual.
- Verificar que los estados de Line Payments y Verified Purchase formen un flujo consistente.

Sin Verified Purchase, un pago aprobado finaliza el flujo. Con Verified Purchase, la orden pasa primero por un estado de verificación. Una configuración incorrecta puede dejar órdenes aprobadas sin validación, bloqueadas o en estados inconsistentes.

La configuración SSL de las llamadas de este módulo se toma de la sección de Line Payments (`payment/linepayment/api_ssl_is_active` y `api_ssl_version`); este módulo no expone campos SSL propios.
