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

        stage('4 - Empacotar Projeto') {
            steps {
                echo '📦 Empacotando projeto...'
                bat """
                    powershell Compress-Archive -Path * -DestinationPath ${PROJECT_NAME}.zip -Force
                """
            }
        }

        stage('5 - Enviar para Nexus') {
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

        stage('6 - Deploy na Hostgator') {
            steps {
                echo '🌐 Fazendo deploy na Hostgator...'
                withCredentials([sshUserPrivateKey(
                    credentialsId: 'hostgator-ssh',
                    keyFileVariable: 'SSH_KEY',
                    usernameVariable: 'SSH_USERNAME'
                )]) {
                    powershell """
                        icacls "\$env:SSH_KEY" /inheritance:r
                        icacls "\$env:SSH_KEY" /grant:r "\$env:USERNAME:R"
                    """
                    bat """
                        scp -o StrictHostKeyChecking=no -P 2222 -i "%SSH_KEY%" ^
                        ${PROJECT_NAME}.zip ^
                        %SSH_USERNAME%@${SSH_HOST}:${DEPLOY_PATH}/
                    """
                    bat """
                        ssh -o StrictHostKeyChecking=no -p 2222 -i "%SSH_KEY%" ^
                        %SSH_USERNAME%@${SSH_HOST} ^
                        "cd ${DEPLOY_PATH} && unzip -o ${PROJECT_NAME}.zip && rm ${PROJECT_NAME}.zip"
                    """
                }
                echo "✅ Deploy ${BUILD_NUMBER} realizado com sucesso!"
            }
        }

    }

    post {
        success {
            echo '✅ Pipeline concluído com sucesso!'
        }
        failure {
            echo '❌ Pipeline falhou! Verifique os logs acima.'
        }
    }
}