#!/bin/bash
echo "  _____       __           _             _               __  __             _ _             "
echo " |  __ \     / _|         | |           (_)             |  \/  |           (_) |            "
echo " | |__) |___| |_ __ _  ___| |_ ___  _ __ _ _ __   __ _  | \  / | ___  _ __  _| |_ ___  _ __ "
echo " |  _  // _ \  _/ _\` |/ __| __/ _ \| '__| | '_ \ / _\` | | |\/| |/ _ \| '_ \| | __/ _ \| '__|"
echo " | | \ \  __/ || (_| | (__| || (_) | |  | | | | | (_| | | |  | | (_) | | | | | || (_) | |   "
echo " |_|  \_\___|_| \__,_|\___|\__\___/|_|  |_|_| |_|\__, | |_|  |_|\___/|_| |_|_|\__\___/|_|   "
echo "                                                  __/ |                                      "
echo "                                                 |___/                                       "
echo "Welcome to RefactoringMonitor"
echo "Select an option:"
echo "1) Load repositories from GitHub into database"
echo "2) Detect refactorings for all existing commits"
echo "3) Detect refactorings for newly added commits"
read -p "Option (1/2/3): " option

case $option in
  1)
    read -p "Enter your GitHub OAuth key: " oauth_key
    read -p "Enter path to input file with list of repositories [./test-repos.txt]: " input_file
    input_file=${input_file:-"${PWD}/test-repos.txt"} # Default value if none provided
    java --add-opens java.base/java.lang=ALL-UNNAMED --add-opens java.base/java.lang.reflect=ALL-UNNAMED -cp RefactoringMiner/RM-fat.jar br.ufmg.dcc.labsoft.refactoringanalyzer.operations.GitProjectFinder  $oauth_key "${input_file}"
    ;;
  2)
    java --add-opens java.base/java.lang=ALL-UNNAMED --add-opens java.base/java.lang.reflect=ALL-UNNAMED -cp RefactoringMiner/RM-fat.jar br.ufmg.dcc.labsoft.refactoringanalyzer.operations.AnalyzeProjects
    ;;
  3)
    java --add-opens java.base/java.lang=ALL-UNNAMED --add-opens java.base/java.lang.reflect=ALL-UNNAMED -cp RefactoringMiner/RM-fat.jar br.ufmg.dcc.labsoft.refactoringanalyzer.operations.AnalyzeNewCommits
    ;;
  *)
    echo "Invalid option. Please select 1, 2, or 3."
    ;;
esac
