<?php


function array_to_table($matriz) 
{   
   echo "<table>";

   // Table header
        foreach ($matriz[0] as $clave=>$fila) {
            echo "<th>".$clave."</th>";
        }

    // Table body
       foreach ($matriz as $fila) {
           echo "<tr>";
           foreach ($fila as $elemento) {
                 echo "<td>".$elemento."</td>";
           } 
          echo "</tr>";
       } 
   echo "</table>";}

function resolve_domain($domain) {
    $dns = '8.8.8.8';  // Google Public DNS
    if (rand(0, 1) == 1) {
        $dns = '208.67.222.222'; // Open DNS
    }
    $ip = `nslookup $domain $dns`; // the backticks execute the command in the shell
    $ips = array();
    if (preg_match_all('/Address: ((?:\d{1,3}\.){3}\d{1,3})/', $ip, $match) > 0) {
        $ips = $match[1];
    }
    return $ips;
}

function open_file_per_line($file) {
    $handle = fopen($file, "r");
    if ($handle) {
        $lines = array();
        while (($line = fgets($handle)) !== false) {
            $lines[] = trim($line);
        }
        return $lines;
        fclose($handle);
    } else {
        return false;
    }
}

function check_valid_resolve_ip($ip, $domain) {
    if ($domain == '_SERVER_HOSTNAME_') {
        return array('label' => 'info', 'msg' => '');
    }
    if (filter_var($ip, FILTER_VALIDATE_IP) == false) {
        return array('label' => 'danger', 'msg' => 'IP INVALIDA');
    }
    $domain_local_ip = get_domain_ip_local_file($domain);
    if ($domain_local_ip['ip'] != $ip) {
        return array('label' => 'danger', 'msg' => 'IP DIFERENTE');
    }
    return array('label' => 'success', 'msg' => '');
}

function get_domain_ip_local_file($domain) {
    $file_lines = open_file_per_line('/etc/userdatadomains');
    $file_ip_nat_lines = open_file_per_line('/var/cpanel/cpnat');
    foreach ($file_lines as $line) {
        $explode = explode('==', $line);
        $explode_two = explode(':', $explode[0]);
        if (trim($explode_two[0]) == trim($domain)) {
            $ip_port = $explode[5];
            $explode_ip = explode(':', $ip_port);
            foreach ($file_ip_nat_lines as $line_ip_nat) {
                $explode_ip_nat = explode(' ', $line_ip_nat);
                if ($explode_ip_nat[0] == $explode_ip[0]) {
                    $explode_ip[0] = $explode_ip_nat[1];
                }
            }
            return array('ip' => $explode_ip[0], 'acc' => trim($explode_two[1]), 'reseller' => trim($explode[1]), 'type' => trim($explode[2]));
        }
    }
}

$all_domains_local = open_file_per_line('/etc/localdomains');
$hostname = gethostname();

$action = $_GET['action'];


$html_ini = "";
$html_ini .= '<html>';
$html_ini .= '<head>';
$html_ini .= '<title>DNS Check Account</title>';
$html_ini .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
$html_ini .= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css">';
$html_ini .= '</head>';
$html_ini .= '<body>';
$html_ini .= '<div class="container-fluid">';
$html_ini .= '<div class="row">';
$html_ini .= '<div class="col-md-12">';
$html_ini .= '<h1>ITFINDEN DNS Check Account WHM Plugin</h1>';
$html_ini .= '</div>';
$html_ini .= '</div>';
$html_ini .= '</div>';
$html_ini .= '<div class="container-fluid">';
$html_ini .= '<div class="row">';
$html_ini .= '<div class="col-md-12">';
$html_ini .= '<table class="table">';
$html_ini .= '<thead>';
$html_ini .= '<tr>';
$html_ini .= '<td>Usuario</td>';
$html_ini .= '<td>Usuario Reseller</td>';
$html_ini .= '<td>Dominio</td>';
$html_ini .= '<td>Local IP</td>';
$html_ini .= '<td>Estado</td>';
$html_ini .= '</tr>';
$html_ini .= '</thead>';
$html_ini .= '<tbody>';


$html_end .= '</tbody>';
$html_end .= '</table>';
$html_end .= '</div>';
$html_end .= '</div>';
$html_end .= '</div>';
$html_end .= '</body>';
$html_end .= '</html>';


$domain_list_all = array();
$domain_list_bad = array();

foreach ($all_domains_local as $domain) {
    $domain_local_acc = get_domain_ip_local_file($domain);
    $resolve_ips = resolve_domain($domain);
    $ips_ = '';
    foreach ($resolve_ips as $ip) {
        if ($domain == $hostname) {
            $domain = '_SERVER_HOSTNAME_';
        }
        $check = check_valid_resolve_ip($ip, $domain);
        $ips_ .= '<span class="label label-' . $check['label'] . '">' . $ip . '</span> ' . $check['msg'] . '<br><br>';
    }
    $ips = rtrim($ips_, '<br>');
    $ip_result_html = $ips != '' ? $ips : '<span class="label label-danger">No Resuelve</span>';
    if ($domain == '_SERVER_HOSTNAME_') {
        $domain = $hostname;
        $domain_local_acc['acc'] = 'root';
    }
    if (($check['label']=='success' )|| ($check['label']=='info' ) || ($domain_local_acc['acc']=='')) {
        $domain_list_all[] = $domain_local_acc['acc']."|".$domain_local_acc['reseller']."|".$domain_local_acc['type']."|".$domain."|".$domain_local_acc['ip']."|".$check['label']."|".$ip_result_html;
    } else {
        $domain_list_bad[] = $domain_local_acc['acc']."|".$domain_local_acc['reseller']."|".$domain_local_acc['type']."|".$domain."|".$domain_local_acc['ip']."|".$check['label']."|".$ip_result_html;
    }
    
     
}



    foreach($domain_list_bad as $key => $value) {
        echo "$key is at $value \n";
    }
 
   /* echo "<pre>";
    print_r($domain_list_all);
    echo "</pre>";
      echo "<pre>";
    print_r($domain_list_bad);
    echo "</pre>";*/
    $html_recordset = "";

    foreach ($domain_list_bad as $value) {
            $pieces = explode("|", $value);
           
        $html_recordset .= "<tr>";
        $html_recordset .= "<td> ". $pieces[0] ."</td>";
        $html_recordset .= "<td> ". $pieces[1] ."</td>";
        $html_recordset .= "<td>(". $pieces[2] .")". $pieces[3]."</td>";
        $html_recordset .= "<td> ". $pieces[4] ."</td>";
        $html_recordset .= "<td> ". $pieces[6] ."</td>";
        $html_recordset .= "</tr>";

        }

$html_full = $html_ini . $html_recordset . $html_end;


echo $html_full;



?>
                               


