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
            <span class="text-bold">1.1. MIGUEL ANGEL GUZMAN CABRERA, </span>
            mayor de edad, hábil por ley, casado,&nbsp;
            portador de la cédula de identidad N° <span class="text-bold"> 4709987 SC</span> domiciliado en esta&nbsp;
            ciudad, que pasa a ser parte integrante&nbsp;
            del presente documento privado y que en adelante denominará <span class="text-bold"> “EL
                ACREEDOR”.</span>
        </p>
        @switch($number_of_clients)
            @case(1)
                <p class="text-parrafo">
                    <span class="text-bold">
                        1.2. {{ strtoupper($contrato_cliente->nombres) }}
                        {{ strtoupper($contrato_cliente->apellido_paterno) }}
                        {{ strtoupper($contrato_cliente->apellido_materno) }},
                    </span> portador de la cédula de identidad N°
                    <span class="text-bold">
                        {{ $contrato_cliente->ci }} {{ $contrato_cliente->ci_expedido }},
                    </span>
                    mayor de edad, hábil por ley, domiciliado en
                    <span class="text-bold">
                        {{ $contrato_cliente->direccion }}
                    </span>
                    en esta ciudad, quien en adelante para efecto del
                    presente documento se lo denominará,
                    <span class="text-bold"> "DEUDOR". </span>
                </p>
            @break

            @case(2)
                <p class="text-parrafo">
                    <span class="text-bold">
                        1.2. {{ strtoupper($contrato_cliente->first_client_nombres) }}
                        {{ strtoupper($contrato_cliente->first_client_apellido_paterno) }}
                        {{ strtoupper($contrato_cliente->first_client_apellido_materno) }},
                    </span> portador de la cédula de identidad N°
                    <span class="text-bold">
                        {{ $contrato_cliente->first_client_ci }} {{ $contrato_cliente->first_client_ci_expedido }},
                    </span> y
                    <span class="text-bold">
                        {{ strtoupper($contrato_cliente->second_client_nombres) }}
                        {{ strtoupper($contrato_cliente->second_client_apellido_paterno) }}
                        {{ strtoupper($contrato_cliente->second_client_apellido_materno) }},
                    </span> portador de la cédula de identidad N°
                    <span class="text-bold">
                        {{ $contrato_cliente->second_client_ci }} {{ $contrato_cliente->second_client_ci_expedido }},
                    </span>
                    ambos mayores de edad, hábil por ley, domiciliado en
                    <span class="text-bold">
                        {{ $contrato_cliente->direccion }}
                    </span>
                    en esta ciudad, quien en adelante para efecto del
                    presente documento se lo denominará,
                    <span class="text-bold"> "DEUDOR". </span>
                </p>
            @break

            @default
                <p class="text-parrafo">
                    <span class="text-bold" style="color:red">
                        ERROR AL LISTAR CLIENTE
                    </span>
            @endswitch
            @if ($add_info_terreno)
                <p class="text-parrafo">
                    <span class="text-bold"> SEGUNDA: (DERECHO PROPIETARIO).- EL ACREEDOR,</span> declara ser aportante
                    de la construcción de un inmueble
                    identificado legalmente en el lote <span class="text-bold"> {{ $contrato_cliente->n_de_lote }},
                    </span> en la U.V.
                    <span class="text-bold"> {{ $contrato_cliente->n_de_uv }}, </span>
                    <span class="text-bold"> zona {{ $contrato_cliente->zona }}, </span>con una superficie de terreno
                    de <span class="text-bold"> {{ $contrato_cliente->terreno_superficie }} m<sup>2</sup>, </span>
                    y con una suma de
                    <span class="text-bold">
                        {{ $contrato_cliente->terreno_valor_total_literalnumber_format }} Dólares Americanos
                        ($us. {{ number_format($contrato_cliente->terreno_valor_total_numeral, 2, '.', '') }})
                    </span>
                    Distrito <span class="text-bold"> {{ $contrato_cliente->numero_distrito }}, </span> que a la
                    fecha se encuentra registrado en Derechos
                    Reales bajo la Matrícula Computarizada N°
                    <span class="text-bold">
                        {{ $contrato_cliente->numero_identificacion_terreno }}.
                    </span>
                </p>
            @else
                <p class="text-parrafo">
                    <span class="text-bold"> SEGUNDA: (DERECHO PROPIETARIO).- EL ACREEDOR,</span> declara ser
                    aportante de la construcción de un inmueble
                    identificado legalmente en el lote <span class="text-bold"> {{ $contrato_cliente->n_de_lote }},
                    </span> en la U.V.
                    <span class="text-bold"> {{ $contrato_cliente->n_de_uv }}, </span>
                    <span class="text-bold"> zona {{ $contrato_cliente->zona }}, </span>con una superficie de
                    terreno de
                    <span class="text-bold"> {{ $contrato_cliente->terreno_superficie }} m<sup>2</sup>, </span>
                    Distrito <span class="text-bold"> {{ $contrato_cliente->numero_distrito }}, </span> que a la
                    fecha se encuentra registrado en Derechos
                    Reales bajo la Matrícula Computarizada N°
                    <span class="text-bold"> {{ $contrato_cliente->numero_identificacion_terreno }}. </span>
                </p>
            @endif
        <p class="text-parrafo">
            <span class="text-bold"> TERCERA: (COLINDANCIAS).- </span> El inmueble identificado legalmente con el N°
            <span class="text-bold"> {{ $contrato_cliente->numero_identificacion_terreno }}, </span>
            objeto del presente contrato, tiene las siguientes colindancias:
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> Al Norte </span> mide <span
                class="text-bold">{{ $contrato_cliente->norte_medida_terreno }} </span> metros y
            colinda con
            lote <span class="text-bold"> {{ $contrato_cliente->norte_colinda_lote }}. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> Al Sur </span> mide <span class="text-bold">
                {{ $contrato_cliente->sur_medida_terreno }} </span> metros y colinda
            con lote
            <span class="text-bold"> {{ $contrato_cliente->sur_colinda_lote }}. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold">Al Este </span> mide <span
                class="text-bold">{{ $contrato_cliente->este_medida_terreno }} </span> metros y colinda
            con lote
            <span class="text-bold"> {{ $contrato_cliente->este_colinda_lote }}. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> Al Oeste </span> mide <span class="text-bold">
                {{ $contrato_cliente->oeste_medida_terreno }} </span> metros y
            colinda con lote
            <span class="text-bold"> {{ $contrato_cliente->oeste_colinda_lote }}. </span>
        </p>
        @if ($add_info_terreno)
            <p class="text-parrafo">
                <span class="text-bold"> CUARTA: (COMPROMISO DE CONSTRUCCIÓN DE INMUEBLE URBANO
                    {{ strtoupper($contrato_cliente->construccion_descripcion) }} CON UNA SUPERFICIE
                    DE {{ $contrato_cliente->construccion_superficie_terreno }} m<sup>2</sup>,
                    CON EL TERRENO MENCIONADO
                    ANTERIORMENTE Y RECONOCIMIENTO DE DEUDA).-</span>
                EL <span class="text-bold"> PROMITENTE ACREEDOR</span> se
                compromete a construir el inmueble signado legalmente con el N°
                <span class="text-bold"> {{ $contrato_cliente->numero_identificacion_terreno }}, </span> señalado
                en las cláusulas segunda y tercera del presente documento, por el valor de la suma de
                <span class="text-bold">
                    {{ $contrato_cliente->construccion_valor_total_literal }} Dólares
                    Americanos
                    ($us. {{ number_format($contrato_cliente->construccion_valor_total_numeral, 2, '.', '') }}.-).
                </span>
            </p>
        @else
            <p class="text-parrafo">
                <span class="text-bold"> CUARTA: (COMPROMISO DE CONSTRUCCIÓN DE INMUEBLE URBANO
                    {{ strtoupper($contrato_cliente->construccion_descripcion) }} CON UNA SUPERFICIE
                    DE {{ $contrato_cliente->construccion_superficie_terreno }} m<sup>2</sup>,
                    Y RECONOCIMIENTO DE DEUDA).-</span>
                EL <span class="text-bold"> PROMITENTE ACREEDOR</span> se
                compromete a construir el inmueble signado legalmente con el N°
                <span class="text-bold"> {{ $contrato_cliente->numero_identificacion_terreno }}, </span> señalado
                en las cláusulas segunda y tercera del presente documento, por el valor de la suma de
                <span class="text-bold"> {{ $contrato_cliente->construccion_valor_total_literal }} Dólares Americanos
                    ($us. {{ number_format($contrato_cliente->construccion_valor_total_numeral, 2, '.', '') }}.-).
                </span>
            </p>
        @endif

        <p class="text-parrafo">
            Con un plazo maximo de entrega de
            <span class="text-bold">
                {{ $contrato_cliente->construccion_cantidad_meses_de_entrega }}
            </span>meses,
            despues de la firma del contrato.
        </p>

        <p class="text-parrafo">
            El presente <span class="text-bold"> COMPROMISO DE CONSTRUCCIÓN DE INMUEBLE URBANO </span> por parte del
            ACREEDOR, se convierte automáticamente
            en un <span class="text-bold">RECONOCIMIENTO DE DEUDA</span> en favor del
            <span class="text-bold"> ACREEDOR </span> por parte del <span class="text-bold"> DEUDOR. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> QUINTA: (PRECIO Y FORMA DE PAGO).- </span> El precio libremente convenido por la
            presente construcción, es de <span class="text-bold">
                {{ $contrato_cliente->construccion_valor_total_literal }} Dólares
                Americanos ($us.
                {{ number_format($contrato_cliente->construccion_valor_total_numeral, 2, '.', '') }}.-).
            </span>
        </p>

        <p class="text-parrafo">
            Para el caso de que el <span class="text-bold"> DEUDOR </span> cancelaré el precio establecido en moneda
            nacional, deberá hacerlo al tipo de cambio comprador del día en el que se efectivicen los pagos.
        </p>

        <p class="text-parrafo">
            El <span class="text-bold"> DEUDOR </span> al aceptar el presente Reconocimiento de Deuda, tal como se
            estipula en este contrato, se obliga y compromete a pagar el precio convenido, en las condiciones
            siguientes:
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> 5.1. La modalidad de pago, es la siguiente:</span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> 5.1.1. </span> A la suscripción del presente documento, el
            <span class="text-bold"> DEUDOR </span> abona la suma de
            <span class="text-bold">
                ($us. {{ number_format($contrato_cliente->construccion_val_couta_inicial_numeral, 2, '.', '') }})
                {{ $contrato_cliente->construccion_val_couta_inicial_literal }}
                Dólares Americanos
            </span>
            a cuenta del precio de la construcción&nbsp;
            con recursos propios en calidad de <span class="text-bold"> "ARRAS". </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> 5.1.2. </span> El saldo del precio pactado, que corresponde a
            <span class="text-bold">
                {{ $contrato_cliente->construccion_val_couta_mensual_literal }} Dólares Americanos
                ($us. {{ number_format($contrato_cliente->construccion_val_couta_mensual_numeral, 2, '.', '') }})
            </span>
            será cancelado en forma mensual en fecha <span class="text-bold"> 15 de cada mes </span>, durante un
            periodo de <span class="text-bold">{{ $contrato_cliente->construccion_cantidad_couta_mensual }}</span>
            meses, completando con estos pagos el precio
            de total de la construcción incluyendo interés.
        </p>

        {{-- aqui va la tabla --}}

        <p class="text-parrafo">
            <span class="text-bold"> SEXTA: (DESISTIMIENTO UNILATERAL).- </span> Para el caso de que el
            <span class="text-bold"> DEUDOR </span> incumpliera con la obligación de cancelar el
            saldo de precio dentro del plazo estipulado en el presente contrato o incumpliera con cualquiera de
            los pagos previsto en la Cláusula Quinta, <span class="text-bold"> EL ACREEDOR </span> podrá
            <span class="text-bold"> UNILATERALMENTE </span> declarar resuelto el presente contrato
            (Art.569 del Código Civil), sin necesidad de intimación o requerimiento judicial o extrajudicial, ni
            de otro acto, formalidad o requisito, situación que dará derecho al
            <span class="text-bold">
                ACREEDOR
            </span>
            a retener el pago efectuado en calidad de&nbsp;
            <span class="text-bold"> ARRAS </span> por el <span class="text-bold"> DEUDOR, </span> es decir, la suma
            de
            <span class="text-bold">
                $us. {{ number_format($contrato_cliente->construccion_val_couta_inicial_numeral, 2, '.', '') }}
                ({{ $contrato_cliente->construccion_val_couta_inicial_literal }} Dólares
                Americanos).
            </span>
            Dicha retención se establece enconsideración y como resarcimiento a
            los daños y perjuicios que el incumplimiento de el
            <span class="text-bold"> DEUDOR </span> le ha originado al <span class="text-bold"> ACREEDOR. </span>
        </p>

        <p class="text-parrafo">
            <span class="text-bold"> SEPTIMA: (ENTREGA DEL INMUEBLE).-</span> En la oportunidad de la entrega del
            inmueble, el<span class="text-bold"> DEUDOR </span> debe tener cumplido en
            su totalidad los pagos con recursos propios, con el derecho legítimo que le asiste al
            <span class="text-bold"> EL ACREEDOR </span> bajo ninguna
            circunstancia, ni excepciones solicitadas, se efectuará la entrega del inmueble, materia del presente
            contrato.
        </p>
        @switch($number_of_clients)
            @case(1)
                <p class="text-parrafo">
                    <span class="text-bold">OCTAVA: (ACEPTACIÓN Y CONFORMIDAD).- </span>
                    Nosotros: <span class="text-bold">
                        MIGUEL ANGEL GUZMAN CABRERA como ACREEDOR
                    </span> por una parte y por la otra
                    <span class="text-bold">
                        {{ strtoupper($contrato_cliente->nombres) }}&nbsp;{{ strtoupper($contrato_cliente->apellido_paterno) }}
                        {{ strtoupper($contrato_cliente->apellido_materno) }},</span> como;
                    <span class="text-bold"> DEUDOR </span>
                    aceptamos el presente contrato en todas y cada una de sus partes, declarando
                    estar conformes y debidamente enterados
                    de su redacción, obligándonos a su fiel y estricto cumplimiento, firmándolo
                    en constancia en un original
                    y dos copias de un mismo tenor y para un solo efecto.
                </p>
            @break

            @case(2)
                <p class="text-parrafo">
                    <span class="text-bold">OCTAVA: (ACEPTACIÓN Y CONFORMIDAD).- </span>
                    Nosotros: <span class="text-bold">
                        MIGUEL ANGEL GUZMAN CABRERA
                        como ACREEDOR
                    </span> por una parte y por la otra
                    <span class="text-bold">
                        {{ strtoupper($contrato_cliente->first_client_nombres) }}
                        {{ strtoupper($contrato_cliente->first_client_apellido_paterno) }}
                        {{ strtoupper($contrato_cliente->first_client_apellido_materno) }},
                    </span> y
                    <span class="text-bold">
                        {{ strtoupper($contrato_cliente->second_client_nombres) }}
                        {{ strtoupper($contrato_cliente->second_client_apellido_paterno) }}
                        {{ strtoupper($contrato_cliente->second_client_apellido_materno) }},
                    </span> como
                    <span class="text-bold"> DEUDOR </span>
                    aceptamos el presente contrato en todas y cada una de sus partes, declarando
                    estar conformes y debidamente enterados
                    de su redacción, obligándonos a su fiel y estricto cumplimiento, firmándolo
                    en constancia en un original
                    y dos copias de un mismo tenor y para un solo efecto.
                </p>
            @break

            @default
                <p class="text-parrafo-center" style="color:red">
                    ERROR AL LISTAR CLIENTE
                </p>
        @endswitch


        <p class="text-parrafo-center">
            @php
                use Carbon\Carbon;
                $carbon = Carbon::parse($contrato_cliente->fecha_firma_contrato)->locale('es');
                $carbon->settings(['formatFunction' => 'translatedFormat']);
            @endphp
            {{ $contrato_cliente->lugar_firma_contrato }} - {{ $carbon->format('d \\d\\e F \\d\\e Y') }}
        </p>
        @switch($number_of_clients)
            @case(1)
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
                                <span class="as-full text-bold">DEUDOR</span>
                            </td>
                        </tr>
                    </table>
                </div>
            @break

            @default
                <div class="footer-page" style="color:red">
                    ERROR AL GENERAR NOMBRES PARA LAS FIRMAS
                </div>
        @endswitch

    </div>

</body>

</html>
