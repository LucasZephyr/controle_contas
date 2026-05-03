pipeline {
    agent any

    environment {
        NEXUS_URL        = 'http://localhost:8081/repository/controle-contas/'
        NEXUS_CREDENTIAL = 'nexus-credentials'
        SONAR_PROJECT    = 'controle-contas'
        PROJECT_NAME     = 'controle_contas'
        SSH_HOST         = 'zephyrpa.online'
        SSH_USER         = 'zephyr98'
        DEPLOY_PATH      = '/home2/zephyr98/public_html/roseli/controle_contas'
    }

    stages {

        stage('1 - Clonar Repositório') {
            steps {
                echo '🔄 Clonando repositório do GitHub...'
                checkout scm
            }
        }

        stage('2 - Análise SonarQube') {
            steps {
                echo '🔍 Analisando qualidade do código...'
                withSonarQubeEnv('SonarQube') {
                    bat """
                        sonar-scanner.bat ^
                        -Dsonar.projectKey=${SONAR_PROJECT} ^
                        -Dsonar.projectName=${SONAR_PROJECT} ^
                        -Dsonar.sources=. ^
                        -Dsonar.exclusions=assets/**,uploads/**,vendor/**
                    """
                }
            }
        }

        stage('3 - Quality Gate') {
            steps {
                echo '🚦 Verificando Quality Gate...'
                timeout(time: 2, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('4 - Análise Semgrep') {
            steps {
                echo '🔐 Analisando segurança com Semgrep...'
                bat """
                    semgrep --config "p/php" ^
                    --output semgrep-report.json ^
                    --json ^
                    --error ^
                    .
                """
            }
            post {
                always {
                    echo '📋 Relatório Semgrep gerado: semgrep-report.json'
                }
                failure {
                    echo '🚨 Semgrep encontrou problemas de segurança! Verifique o relatório.'
                }
            }
        }

        stage('5 - Empacotar Projeto') {
            steps {
                echo '📦 Empacotando projeto...'
                bat """
                    powershell Compress-Archive -Path * -DestinationPath ${PROJECT_NAME}_full.zip -Force
                    powershell Compress-Archive ^
                    -Path index.php,login.php,logout.php,inserirContasMes.php,classes,includes,processa ^
                    -DestinationPath ${PROJECT_NAME}.zip -Force
                """
            }
        }

        stage('6 - Enviar para Nexus') {
            steps {
                echo '🚀 Enviando artefato para o Nexus...'
                withCredentials([usernamePassword(
                    credentialsId: "${NEXUS_CREDENTIAL}",
                    usernameVariable: 'NEXUS_USER',
                    passwordVariable: 'NEXUS_PASS'
                )]) {
                    bat """
                        curl -u %NEXUS_USER%:%NEXUS_PASS% ^
                        --upload-file ${PROJECT_NAME}.zip ^
                        ${NEXUS_URL}${PROJECT_NAME}-${BUILD_NUMBER}.zip
                    """
                }
            }
        }

        // stage('7 - Deploy na Hostgator') — temporariamente desabilitado

    }

    post {
        success {
            echo '✅ Pipeline concluído com sucesso!'
        }
        failure {
            echo '❌ Pipeline falhou! Verifique os logs acima.'
        }
        always {
            archiveArtifacts artifacts: 'semgrep-report.json', allowEmptyArchive: true
        }
    }
}