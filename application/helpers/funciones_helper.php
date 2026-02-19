<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



/**
 * Imprimir arreglos de una forma mas legible
 * @author oagarzond
 * @param mixed $objVar Arreglo o cadena para mostrar por pantalla con formato
 */
if (!function_exists("pr")) {

    function pr($objVar) {
        echo "<div align='left'>";
        if (is_array($objVar) or is_object($objVar)) {
            echo "<pre>";
            print_r($objVar);
            echo "</pre>";
        } else {
            echo str_replace("\n", "<br>", $objVar);
        }
        echo "</div><hr>";
    }

}

/**
 * Funcion para validar si una fecha es valida.
 *
 * Esta funcion se utiliza para validar si la fecha pasada por parametro es valida o no Ej. 2011-02-29 no es una fecha valida.
 * @author javier-sanchez
 * @param string $cadena Arreglo o cadena para mostrar por pantalla con formato.
 * @param array $arrCaracteres Arreglo o cadena para mostrar por pantalla con formato.
 * @return string Retorna la cadena formateada o escapada.
 */
if (!function_exists("es_fecha_valida")) {

    function es_fecha_valida($fecha) {
        if (strstr($fecha, "-")) {
            $data = explode("-", $fecha);
            if (strlen($data[0]) != 4)
                return false;
            return(@checkdate(intval($data[1]), intval($data[2]), intval($data[0])));
        }
        elseif (strstr($fecha, "/")) {
            $data = explode("/", $fecha);
            if (strlen($data[2]) != 4)
                return false;
            return(@checkdate(intval($data[0]), intval($data[1]), intval($data[2])));
        }
    }

}

/**
 * Esta funcion se utiliza para darle formato a la fecha pasada por parametro, 
 * es decir si se pasa el formato YYYY-MM-DD se retorna la fecha en formato DD/MM/YYYY y viceversa.
 * @author oagarzond
 * @param	date	$fecha	Fecha
 * @return	string	Retorna la fecha formateada o vacio si la fecha no es valida
 */
if (!function_exists("formatear_fecha")) {

    function formatear_fecha($fecha) {
        if (es_fecha_valida($fecha)) {
            if (strstr($fecha, "-")) {
                $data = explode("-", $fecha);
                return $data[2] . "/" . $data[1] . "/" . $data[0];
            } elseif (strstr($fecha, "/")) {
                $data = explode("/", $fecha);
                return $data[2] . "-" . $data[0] . "-" . $data[1];
            }
        } else
            return "";
    }

}

/**
 * @author oagarzond
 * @param	String	$ruta_imagen	Ruta relativa con el nombre de la imagen y su extension
 * @return	URL absoluta de la imagen deseada
 */
if (!function_exists("base_url_images")) {
    function base_url_images($ruta_imagen = '') {
        $CI = & get_instance();
        $url_images = $CI->config->base_url() . 'images/';
        if (strlen($ruta_imagen) > 0) {
            $url_images .= $ruta_imagen;
        }
        return $url_images;
    }
}

/**
 * Formatear el numero del celular
 * @author bmottag
 * @param	String	$mobile	Numero de celular
 * @return	formatea valor del numero de celular
 */
if (!function_exists("mobile_adjustment")) {
    function mobile_adjustment($mobile = '') {
        $count = strlen($mobile); 
        $num_tlf1 = substr($mobile, 0, 3); 
        $num_tlf2 = substr($mobile, 3, 3); 
        $num_tlf3 = substr($mobile, 6, 2); 
        $num_tlf4 = substr($mobile, -2); 
        return $count == 10?"$num_tlf1 $num_tlf2 $num_tlf3 $num_tlf4":chunk_split($mobile,3," ");
    }
}

/**
 * Enviar correo electronico
 * @author bmottag
 * @param mixed $objVar Arreglo o cadena para mostrar por pantalla con formato
 */
if (!function_exists("send_notification")) {

    function send_notification($arrDatos) {

        //revisar si se envia correo o se envia mensaje de texto y a quien se le envia
        $arrParam = array("idNotification" => $arrDatos["idNotification"]);

        $CI = &get_instance();
        $CI->load->model('general_model');
        $notificationSettings = $CI->general_model->get_notifications_access($arrParam);

        if( $notificationSettings)
        {
            if($arrDatos["msjPhone"]){
                //configuracion para envio de mensaje de texto
                $CI->load->library('encrypt');
                require 'vendor/Twilio/autoload.php';

                //busco datos parametricos twilio
                $arrParam = array(
                    "table" => "parametric",
                    "order" => "id_parametric",
                    "id" => "x"
                );
                $parametric = $CI->general_model->general_model->get_basic_search($arrParam);						
                $dato1 = $CI->encrypt->decode($parametric[3]["value"]);
                $dato2 = $CI->encrypt->decode($parametric[4]["value"]);
                $twilioPhone = $parametric[5]["value"];
                
                $client = new Twilio\Rest\Client($dato1, $dato2);
            }

            foreach($notificationSettings as $envioAlerta):
                //envio correo 
                if($envioAlerta['email'] && $arrDatos["msjEmail"])
                {
                    $to = $envioAlerta['email'];

                    $headers  = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= 'From: VCI APP <info@v-contracting.ca>' . "\r\n";
                    //enviar correo
                    $envio = mail($to, $arrDatos["subjet"], $arrDatos["msjEmail"], $headers);
                }

                //envio mensaje de texto
                if($envioAlerta['movil'] && $arrDatos["msjPhone"]){
                    $to = '+1' . $envioAlerta['movil'];

                    $client->messages->create(
                        $to,
                        array(
                            'from' => $twilioPhone,
                            'body' => $arrDatos["msjPhone"]
                        )
                    );
                }
            endforeach;
        }
    }

}

/**
 * Send Twilio message
 * @author bmottag
 * @param mixed $objVar
 */
if (!function_exists("send_twilio_message")) {

    function send_twilio_message($arrDatos) 
    {
        $CI = &get_instance();
        $CI->load->model('general_model');
        if($arrDatos["msjPhone"] && $arrDatos["userMovil"]){
            //configuracion para envio de mensaje de texto
            $CI->load->library('encrypt');
            require 'vendor/Twilio/autoload.php';

            //busco datos parametricos twilio
            $arrParam = array(
                "table" => "parametric",
                "order" => "id_parametric",
                "id" => "x"
            );
            $parametric = $CI->general_model->general_model->get_basic_search($arrParam);						
            $dato1 = $CI->encrypt->decode($parametric[3]["value"]);
            $dato2 = $CI->encrypt->decode($parametric[4]["value"]);
            $twilioPhone = $parametric[5]["value"];
            
            $client = new Twilio\Rest\Client($dato1, $dato2);
            //envio mensaje de texto
            $to = '+1' . $arrDatos["userMovil"];
            $client->messages->create(
                $to,
                array(
                    'from' => $twilioPhone,
                    'body' => $arrDatos["msjPhone"]
                )
            );

        }
    }
}

/**
 * Calculate time difference in hours
 * @author bmottag
 * @param $dateStart, $fechaCierre
 */
if (!function_exists("calculate_time_difference_in_hours")) {

    function calculate_time_difference_in_hours($dateStart, $dateFinish) {
        // Convertir a objetos DateTime
        $start = DateTime::createFromFormat('Y-m-d G:i:s', $dateStart);
        $finish = DateTime::createFromFormat('Y-m-d G:i:s', $dateFinish);
    
        if (!$start || !$finish) {
            return "Error: Formato de fecha incorrecto";
        }
    
        // Calcular la diferencia en segundos
        $differenceInSeconds = $finish->getTimestamp() - $start->getTimestamp();
    
        // Convertir segundos a horas con decimales
        $differenceInHours = $differenceInSeconds / 3600;
    
        return round($differenceInHours, 2); // Redondear a 2 decimales
    }

}

/**
 * convert_hours_minutes
 * @author bmottag
 * @param $horasDecimal
 */
if (!function_exists("convert_hours_minutes")) {
    function convert_hours_minutes($horasDecimal) {
        if ($horasDecimal == 0) {
            return "-"; // Si es 0, devuelve "-"
        }
    
        $horas = floor($horasDecimal); // Parte entera (horas)
        $minutos = round(($horasDecimal - $horas) * 60); // Convertir la parte decimal en minutos
    
        return sprintf("%d hrs %02d min", $horas, $minutos);
    }
}

/**
 * convert_hours_minutes
 * @author bmottag
 * @param $horasDecimal
 */
if (!function_exists("convert_time_to_hours_minutes")) {
    function convert_time_to_hours_minutes($time) {
        list($hours, $minutes, $seconds) = explode(":", $time); // Separar HH, MM, SS

        // Redondear los segundos a minutos si es necesario
        /*
        if ($seconds >= 30) {
            $minutes++;
        }
        */

        return sprintf("%d hrs %02d min", $hours, $minutes);
    }
}

if (!function_exists('working_hours_in_hours_format')) {
    function working_hours_in_hours_format($dateStart, $dateFinish) {
        // Calcular diferencia en minutos
        $minutos = abs(strtotime($dateFinish) - strtotime($dateStart)) / 60;
        $minutos = round($minutos);

        // Convertir a horas decimales
        $horas = $minutos / 60;
        $horas = round($horas, 2);

        // Separar parte entera y decimal
        $justHours = intval($horas);
        $decimals = $horas - $justHours;

        // Redondeo personalizado a bloques de 15/30/45 min o 1 hora
        if ($decimals < 0.12) {
            $transformation = 0;
        } elseif ($decimals >= 0.12 && $decimals < 0.37) {
            $transformation = 0.25;
        } elseif ($decimals >= 0.37 && $decimals < 0.62) {
            $transformation = 0.5;
        } elseif ($decimals >= 0.62 && $decimals < 0.87) {
            $transformation = 0.75;
        } else {
            $transformation = 1;
        }

        // Resultado final ajustado
        $workingHours = $justHours + $transformation;

        return $workingHours . " hrs";
    }
}



