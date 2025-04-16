-- dodawanie pracownika

curl --location 'localhost:8080/api/employees' \
--form 'name="John"' \
--form 'surname="Doe"'

-- dodawanie worktime

curl --location 'localhost:8080/api/worktimes' \
--form 'employeeUuid="7d780b05-5429-426d-8a13-d6d5fc5f9207"' \
--form 'start="1990-07-7 10:00:00"' \
--form 'stop="1990-07-7 20:15:00"'

-- podsumowanie 

curl --location 'localhost:8080/api/worktimes/7d780b05-5429-426d-8a13-d6d5fc5f9207/1990-07-05'