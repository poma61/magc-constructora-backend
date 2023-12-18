{{-- es una copia no esta modificado nada --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('src/css/pdf/contrato-pdf.css') }}">
    <title>Contrato</title>
</head>

<body>
    <div class="main-pdf">
        <div class=header-title>
            <h1>
                CONTRATO PRIVADO DE COMPROMISO DE CONSTRUCCIÓN DE INMUEBLE URBANO Y RECONOCIMIENTO DE DEUDA
            </h1>
        </div>

        <p class="text-parrafo">
            Conste por el presente documento privado que adquirirá fuerza y valor de instrumento público a solo
            reconocimiento de firmas, de conformidad al Art. 1.297 del Código Civil, constituyéndose ley entre
            partes al tenor del Artículo 519 del mismo cuerpo legal, que de común acuerdo y plena capacidad entre
            la partes intervinientes se ha convenido este contrato, sujeto al tenor de las cláusulas siguientes:
        </p>

        <p class="text-parrafo">
            <span class="text-bold">PRIMERA: (DE LAS PARTES).-</span> Intervienen en la celebración del presente
            contrato, las siguientes partes:
        </p>
        <p class="text-parrafo">
            <span class="text-bold">1.1. MIGUEL ANGEL GUZMAN CABRERA,cmayor de edad, hábil por ley, casado,
                portador de la cédula de identidad N° <span class="text-bold">4709987 SC</span> domiciliado en esta
                ciudad, que pasa a ser parte integrante
                del presente documento privado y que en adelante denominará <span class="text-bold">“EL
                    ACREEDOR”.</span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold">
                1.2. {{ strtoupper($contrato_cliente->nombres) }}&nbsp;
                {{ strtoupper($contrato_cliente->apellido_paterno) }}&nbsp;
                {{ strtoupper($contrato_cliente->apellido_materno) }},
            </span>&nbsp;portador de la cédula de identidad para
            N° <span class="text-bold"> {{ $contrato_cliente->ci }}&nbsp;{{ $contrato_cliente->ci_expedido }}, </span>
            mayor de edad, hábil por ley, domiciliada {{ $contrato_cliente->direccion }} en esta ciudad, quien en
            adelante para efecto del
            presente documento se lo denominará,
            <span class="text-bold">“EL DEUDOR”.</span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> SEGUNDA: (DERECHO PROPIETARIO).- EL ACREEDOR,</span> declara ser aportante de la
            construcción de un inmueble
            identificado legalmente en el lote <span class="text-bold"> {{ $contrato_cliente->n_de_lote }}, </span>en
            la U.V.
            <span class="text-bold"> {{ $contrato_cliente->n_de_uv }}, </span>
            <span class="text-bold"> zona {{ $contrato_cliente->zona }}, </span>con una superficie de terreno
            de <span class="text-bold"> {{ $contrato_cliente->superficie_terreno }} m<sup>2</sup>, </span>
            Distrito <span class="text-bold"> {{ $contrato_cliente->numero_distrito }}, </span> que a la
            fecha se encuentra registrado en Derechos
            Reales bajo la Matrícula Computarizada N° <span
                class="text-bold">{{ $contrato_cliente->numero_identificacion_terreno }}. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> TERCERA: (COLINDANCIAS).- </span> El inmueble identificado legalmente con el N°
            <span class="text-bold"> {{ $contrato_cliente->numero_identificacion_terreno }}, </span>
            objeto del presente contrato, tiene las siguientes colindancias:
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> Al Norte </span> mide {{ $contrato_cliente->norte_medida_terreno }} metros y
            colinda con
            lote <span class="text-bold"> {{ $contrato_cliente->norte_colinda_lote }}. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> Al Sur </span> mide {{ $contrato_cliente->sur_medida_terreno }} metros y colinda
            con lote
            <span class="text-bold"> {{ $contrato_cliente->sur_colinda_lote }}. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold">Al Este </span> mide {{ $contrato_cliente->este_medida_terreno }} metros y colinda
            con lote
            <span class="text-bold"> {{ $contrato_cliente->este_colinda_lote }}. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> Al Oeste </span> mide {{ $contrato_cliente->oeste_medida_terreno }} metros y
            colinda con lote
            <span class="text-bold"> {{ $contrato_cliente->oeste_colinda_lote }}. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> CUARTA: (COMPROMISO DE CONSTRUCCIÓN DE INMUEBLE URBANO CON UNA SUPERFICIE
                DE {{ $contrato_cliente->superficie_terreno }} m<sup>2</sup> Y RECONOCIMIENTO DE
                DEUDA).-</span>
            EL <span class="text-bold"> PROMITENTE ACREEDOR</span> se
            compromete a construir el inmueble signado legalmente con el N°
            <span class="text-bold"> {{ $contrato_cliente->numero_identificacion_terreno }}, </span> señalado
            en las cláusulas segunda y tercera del presente documento, por el valor de la suma de
            {{ $contrato_cliente->valor_construccion_literal }} Dólares Americanos
            ($us. {{ $contrato_cliente->valor_construccion_numeral }}.-).
        </p>

        <p class="text-parrafo">
            El presente <span class="text-bold"> COMPROMISO DE CONSTRUCCIÓN DE INMUEBLE URBANO </span> por parte del
            ACREEDOR, se convierte automáticamente
            en un <span class="text-bold">RECONOCIMIENTO DE DEUDA</span> en favor del
            <span class="text-bold"> ACREEDOR </span> por parte del <span class="text-bold"> DEUDOR. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> QUINTA: (PRECIO Y FORMA DE PAGO).- </span> El precio libremente convenido por la
            presente construcción, es de {{ $contrato_cliente->valor_construccion_literal }} Dólares
            Americanos ($us. {{ $contrato_cliente->valor_construccion_numeral }}.-).
        </p>

        <p class="text-parrafo">
            Para el caso de que <span class="text-bold">EL DEUDOR</span> cancelaré el precio establecido en moneda
            nacional, deberá hacerlo al tipo de cambio comprador del día en el que se efectivicen los pagos.
        </p>

        <p class="text-parrafo">
            <span class="text-bold">EL DEUDOR</span> al aceptar el presente Reconocimiento de Deuda, tal como se
            estipula en este contrato, se obliga y compromete a pagar el precio convenido, en las condiciones
            siguientes:
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> 5.1. La modalidad de pago, es la siguiente:</span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> 5.1.1. </span> A la suscripción del presente documento,
            <span class="text-bold"> EL DEUDOR </span> abona la suma de
            <span class="text-bold">
                &nbsp;($us.{{ $contrato_cliente->valor_couta_inicial_numeral }})&nbsp;
                {{ $contrato_cliente->valor_couta_inicial_literal }}&nbsp;
                Dólares Americanos&nbsp;
            </span>
            a cuenta del precio de la construcción&nbsp;
            con recursos propios en calidad de <span class="text-bold"> "ARRAS". </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> 5.1.2. </span> El saldo del precio pactado, que corresponde a&nbsp;
            <span class="text-bold">
                {{ $contrato_cliente->valor_couta_mensual_literal }} Dólares Americanos&nbsp;
            </span>
            <span class="text-bold"> ($us. {{ $contrato_cliente->valor_couta_mensual_numeral }}) </span>
            será cancelado en forma mensual en fecha 15 de cada mes, durante un periodo de&nbsp;
            120 meses, completando con estos pagos el precio&nbsp;
            de total de la construcción incluyendo interés.
        </p>

        {{-- aqui va la tabla --}}
        <p class="text-parrafo">
            <span class="text-bold"> SEXTA: (DESISTIMIENTO UNILATERAL).-</span> Para el caso de que <span
                class="text-bold"> EL DEUDOR </span> incumpliera con la obligación de cancelar el&nbsp;
            saldo de precio dentro del plazo estipulado en el presente contrato o incumpliera con cualquiera de&nbsp;
            los pagos previsto en la Cláusula Quinta, <span class="text-bold"> EL ACREEDOR </span> podrá
            <span class="text-bold"> UNILATERALMENTE </span> declarar resuelto el presente contrato&nbsp;
            (Art.569 del Código Civil), sin necesidad de intimación o requerimiento judicial o extrajudicial, ni&nbsp;
            de otro acto, formalidad o requisito, situación que dará derecho al
            <span class="text-bold">
                &nbsp;ACREEDOR&nbsp;
            </span>
            a retener el pago efectuado en calidad de&nbsp;
            <span class="text-bold"> ARRAS </span> por <span class="text-bold"> EL DEUDOR, </span> es decir, la suma
            de&nbsp;
            $us. {{ $contrato_cliente->valor_couta_inicial_numeral }}
            ({{ $contrato_cliente->valor_couta_inicial_literal }} Dólares&nbsp;
            Americanos). Dicha retención se establece enconsideración y como resarcimiento a&nbsp;
            los daños y perjuicios que el incumplimiento de&nbsp;
            <span class="text-bold"> EL DEUDOR </span> le ha originado al <span class="text-bold"> ACREEDOR. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> SEPTIMA: (ENTREGA DEL INMUEBLE).-</span> En la oportunidad de la entrega del
            inmueble, <span class="text-bold"> EL DEUDOR </span> debe tener cumplido en
            su totalidad los pagos con recursos propios, con el derecho legítimo que le asiste al
            <span class="text-bold"> EL ACREEDOR </span> bajo ninguna
            circunstancia, ni excepciones solicitadas, se efectuará la entrega del inmueble, materia del presente
            contrato.
        </p>

        <p class="text-parrafo">
            <span class="text-bold">OCTAVA: (ACEPTACIÓN Y CONFORMIDAD).- Nosotros: MIGUEL ANGEL GUZMAN CABRERA
                como ACREEDOR </span> por una parte y por la otra
            <span class="text-bold">
                {{ strtoupper($contrato_cliente->nombres) }}&nbsp;{{ strtoupper($contrato_cliente->apellido_paterno) }}
                {{ strtoupper($contrato_cliente->apellido_materno) }},</span> como&nbsp;
            <span class="text-bold">
                EL DEUDOR
            </span>
            aceptamos el presente contrato en todas y cada una de sus partes, declarando&nbsp;
            estar conformes y debidamente enterados&nbsp;
            de su redacción, obligándonos a su fiel y estricto cumplimiento, firmándolo&nbsp;
            en constancia en un original&nbsp;
            y dos copias de un mismo tenor y para un solo efecto.
        </p>

        <p class="text-parrafo-center">
            @php
                use Carbon\Carbon;
                $carbon = Carbon::parse($contrato_cliente->fecha_firma_contrato)->locale('es');
                $carbon->settings(['formatFunction' => 'translatedFormat']);
            @endphp
            {{ $contrato_cliente->lugar_firma_contrato }} - {{ $carbon->format('d \\d\\e F \\d\\e Y') }}
        </p>

        <div class="footer-page">
            <table>
                <tr>
                    <td>
                        <span
                            class="as-full text-bold">.........................................................................................</span>
                        <span class="as-full text-bold">MIGUEL ANGEL GUZMAN CABRERA</span>
                        <span class="as-full text-bold">EL ACREEDOR</span>
                    </td>
                    <td>
                        <span
                            class="as-full text-bold">.........................................................................................</span>
                        <span class="as-full text-bold">
                            {{ strtoupper($contrato_cliente->nombres) }}
                            {{ strtoupper($contrato_cliente->apellido_paterno) }}
                            {{ strtoupper($contrato_cliente->apellido_materno) }}
                        </span>
                        <span class="as-full text-bold">EL DEUDOR</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>
