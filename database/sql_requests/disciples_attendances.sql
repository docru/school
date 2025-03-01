SELECT U.id                   as uid,
       G.id                   as gid,
       G.name                 as group_name,
       ANY_VALUE(GGSD.status) as status_day,
       ANY_VALUE(GGSD.id)     as gsd_id,
       GGSD.date              as `date`,
       COUNT(A.user_id)       as attendance
FROM users U
         LEFT JOIN group_users GU ON U.id = GU.user_id
         LEFT JOIN `groups` G ON GU.group_id = G.id
         LEFT JOIN (SELECT *
                    FROM group_school_days GSD
                             LEFT JOIN (SELECT group_id as gid, MAX(date) as date_max
                                        FROM group_school_days
                                        GROUP BY gid) GSD_MAX
                                       ON GSD.group_id = GSD_MAX.gid AND GSD.date = GSD_MAX.date_max
                    WHERE gid IS NOT NULL) GGSD
                   ON GGSD.group_id = G.id
         LEFT JOIN attendances A ON A.group_school_day_id = GGSD.id AND U.id = A.user_id
WHERE GU.role = 'disciple'
  AND GU.status = 'active'
  AND G.status = 'open'
GROUP BY U.id, G.id, GGSD.date;
