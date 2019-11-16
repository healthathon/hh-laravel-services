ALTER TABLE `short_health_assessments` ADD `is_hospitalisation` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `is_scoreable`;
ALTER TABLE `user_tasks` ADD `reset_week_counter` ENUM('1','2') NOT NULL DEFAULT '2' AFTER `last_done_date`;
ALTER TABLE `user_tasks` ADD `new_start_date` DATE NULL DEFAULT CURRENT_TIMESTAMP AFTER `reset_week_counter`;
UPDATE `user_tasks` SET `new_start_date`=`start_date`;

CREATE TABLE `user_task_cron_status` ( `id` INT NOT NULL AUTO_INCREMENT , `last_record_count` INT NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

