#  Demon Deburgers – Gestión de Mesas

Aplicación web para la gestión de mesas en un restaurante, diseñada para optimizar el trabajo de los camareros y mantener un registro completo de la ocupación de las mesas en tiempo real.

---

##  Estructura del restaurante

### Sales disponibles
- **3** terrazas  
- **2** comedores  
- **4** salas privadas  

### Distribución
La distribución de las mesas es **libre**, definida por los miembros del grupo.  
Cada sala puede tener un **número variable de mesas** y **capacidades distintas**.

### Información de cada mesa
- Número o identificador único  
- Capacidad (número de sillas)  
- Sala donde se encuentra  

---

##  Funcionalidades de la aplicación

###  Login / Logout de camareros
- Los camareros **ya existen** en la base de datos.
- No es necesario dar de alta ni baja.
- Cada acción queda **registrada** con el identificador del camarero.

###  Gestión de mesas
- Estado: **libre** o **ocupada**
- El camarero puede:
  - Marcar una mesa como **ocupada** cuando llega un cliente.
  - Marcar la mesa como **libre** cuando el cliente se marcha.

###  Visualización
- Lista de **salas** con su capacidad total.
- Disponibilidad de mesas **en tiempo real**.
- Capacidad individual de cada mesa.

###  Histórico de ocupaciones
Se almacena:
- Hora y día de ocupación  
- Hora y día de liberación  
- Camarero responsable  

Filtros disponibles:
- Por recurso (**mesa concreta**)  
- Por sala / ubicación  

Esto permite analizar la **frecuencia de uso de cada mesa**.


---

## Flujo de uso

1. El camarero hace **login**.  
2. Consulta las **salas y mesas disponibles**.  
3. Marca una mesa como **ocupada** → se registra `hora_inici` y camarero.  
4. Cuando el cliente se marcha, marca la mesa como **libre** → se registra `hora_final`.  
5. El sistema actualiza la disponibilidad y guarda el **histórico completo**.





## Enlaces de interés

Figma: https://www.figma.com/design/BaIVlSJViiAF9nroV4T9hF/P01?node-id=0-1&t=edQZgT8nqUyiADvB-1

Contrato de compromiso de equipo: https://drive.google.com/file/d/1zTnWaQllUZQFd7wy7b1I2ncv0W-tJ8IP/view?usp=drive_link

Diagrama de gantt: https://docs.google.com/spreadsheets/d/1KOelFm3tKgIu0rdXoAM72R66sy920d7DnjyXl0VxK2w/edit?usp=sharing

Daily Scrum: https://docs.google.com/spreadsheets/d/1kwmFBOp1JPYDCxmtgtz83MG2hQloYtLE/edit?usp=sharing&ouid=115030546778315437875&rtpof=true&sd=true

Diagrama relacional: https://drive.google.com/file/d/1HOngSmuo2rHsETW8u6aFDoO8863JrBUR/view?usp=drive_link

BBDD: https://docs.google.com/spreadsheets/d/15XZbBm_P7VpquNPqNV6gOc-d1dVXx_Qx/edit?usp=drive_link&ouid=111248879625368351140&rtpof=true&sd=true 

Diagrama de flujo: https://drive.google.com/file/d/1ONegD5h8rna508jDCTXXuAVTjXOmAIua/view?usp=drive_link

Contenido grafico de nuestro proyecto: https://drive.google.com/drive/folders/1MbPNLAJj6mKtVcSga4L4_vp1kNlrQ40G?usp=drive_link

