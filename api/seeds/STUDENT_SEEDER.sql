INSERT INTO `users` (`full_name`, `first_name`, `last_name`, `type`, `username`) VALUES ('Alejandro Sanchez', 'Alejandro', 'Sanchez', 'student', 'aalejo@gmail.com');
INSERT INTO `users` (`full_name`, `first_name`, `last_name`, `type`, `username`) VALUES ('Ramon Peralta', 'Ramon', 'Peralta', 'student', 'a@4geeks.co');

INSERT INTO `students` (`user_id`) VALUES (1);
INSERT INTO `teachers` (`user_id`) VALUES (2);

INSERT INTO `tasks` (`id`, `status`, `type`, `title`, `associated_slug`, `github_url`, `revision_status`, `student_user_id`, `description`, `created_at`, `updated_at`) VALUES (NULL, 'pending', 'assignment', 'Instagram with Bootstrap', 'instagram-bootstrap', NULL, 'pending', 1, '', NULL, NULL);

INSERT INTO `profiles` (`slug`, `name`, `duration_in_hours`, `week_hours`, `description`) VALUES ('full-stacl-ft', 'Full Stack Full Time', '678', NULL, '');

INSERT INTO `locations` (`slug`, `name`, `country`, `address`) VALUES ('miami-dade-college', 'mdc-iii', 'usa', 'Calle 8, Interamerican.');

INSERT INTO `cohorts` (`slug`, `name`, `current_day`, `streaming_slug`, `location_id`, `profile_id`, `stage`, `language`, `slack_url`) VALUES ('mdc-ii', 'MDC-II', '0', NULL, '1', '1', 'prework', 'es', 'slack.com/mdc-iii');

/* add student 1 to cohort 1 */
INSERT INTO `cohort_student` (`student_user_id`, `cohort_id`) VALUES ('1', '1');
/* add teacher 1 to cohort 1 */
INSERT INTO `cohort_teacher` (`teacher_user_id`, `cohort_id`) VALUES ('1', '1');