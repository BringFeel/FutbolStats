# FútbolStats

> Desarrollado por: Francisco Santiago Vizgarra, Tobias Benitez e Ian Basly

## 📋 Resumen del Proyecto

**FútbolStats** es una base de datos diseñada para la administración y el análisis integral de la información de un equipo deportivo, con un enfoque principal en el fútbol. El sistema busca centralizar y facilitar tanto la gestión técnica y médica como el análisis del rendimiento colectivo e individual de los jugadores.

La aplicación fue desarrollada para resolver la problemática de la **"planificación, ejecución y análisis de un partido de fútbol"**.

## ✨ Funcionalidades Principales

Este sistema ofrece una solución completa para el seguimiento de un equipo, cubriendo las siguientes áreas:

  * **Gestión de Jugadores:** Registra datos personales y deportivos de cada jugador, incluyendo su posición, número de camiseta y estado actual.
  * **Registro de Partidos y Resultados:** Almacena información detallada de los partidos, como rivales, fechas, resultados y si se jugó de local o visitante.
  * **Control de Alineaciones y Estadísticas:** Permite controlar las alineaciones por partido, los minutos jugados y registrar estadísticas individuales como goles, asistencias y tarjetas.
  * **Administración de Entrenamientos:** Gestiona las sesiones de entrenamiento y controla la asistencia de los jugadores a las mismas.
  * **Seguimiento de Lesiones:** Mantiene un historial médico y de lesiones de los jugadores para una mejor gestión médica.
  * **Gestión de Entrenadores:** Administra la información de los entrenadores y su participación específica en partidos y entrenamientos.
  * **Soporte para Análisis:** Facilita el análisis de datos históricos para la toma de decisiones tácticas, médicas y administrativas.

## ✅ Problemas que Soluciona

  * **Seguimiento Individualizado:** Permite un monitoreo detallado del rendimiento y la participación de cada jugador.
  * **Gestión Médica Centralizada:** Facilita el acceso y la consulta al historial de lesiones de los jugadores.
  * **Análisis Técnico y Táctico:** Proporciona datos para la evaluación de alineaciones, minutos jugados y otras estadísticas de rendimiento.
  * **Control de Asistencia:** Lleva un registro claro de la presencia de los jugadores en los entrenamientos.
  * **Análisis Estratégico a Largo Plazo:** Los datos recopilados sirven como base para la toma de decisiones estratégicas sobre contratos, rotaciones y suplencias.

## 🗄️ Estructura de la Base de Datos

El sistema se apoya en una base de datos relacional para organizar toda la información de manera eficiente. Las tablas principales involucradas son:

  * `jugadores`: Contiene los datos personales y deportivos de los jugadores.
  * `entrenadores`: Almacena la información del cuerpo técnico.
  * `partidos`: Registra los detalles de cada encuentro, como rival, fecha y resultado.
  * `entrenamientos`: Guarda la información sobre las sesiones de entrenamiento.
  * `lesiones`: Lleva un historial de las lesiones de los jugadores.
  * `alineacion`: Define qué jugadores participaron en un partido, si fueron titulares y los minutos que jugaron.
  * `estadisticas_partido`: Registra las estadísticas individuales (goles, asistencias, tarjetas) de un jugador en un partido específico.
  * `asistencia_entrenamiento`: Controla la asistencia de los jugadores a cada sesión de entrenamiento.
  * `entrenadores_eventos`: Asocia a los entrenadores con los eventos en los que participan, ya sean partidos o entrenamientos.

## 📊 Usos Adicionales Potenciales

La estructura de la base de datos permite expandir el sistema para incluir:

  * Creación de reportes estadísticos automáticos.
  * Visualización gráfica del rendimiento por jugador o partido.
  * Un sistema de alertas para jugadores con baja asistencia o en proceso de recuperación de lesiones.
  * Análisis de la efectividad y el impacto del cuerpo técnico.
