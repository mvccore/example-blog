CREATE DATABASE "mvccore_blog";
\c


DROP SEQUENCE IF EXISTS "public"."users_next_id";
DROP TABLE IF EXISTS "public"."users";

CREATE SEQUENCE "public"."users_next_id"
    INCREMENT 1
    START 1
    MINVALUE 1;

CREATE TABLE "public"."users" (
    "id" integer NOT NULL,
    "active" smallint DEFAULT 1 NOT NULL,
    "admin" smallint DEFAULT 0 NOT NULL,
    "user_name" character varying(50) NOT NULL,
    "full_name" character varying(100) NOT NULL,
    "email" character varying(200) NOT NULL,
    "password_hash" character varying(60) NOT NULL,
    "avatar_url" character varying(1000),
    "permissions" character varying(1000),
    "roles" character varying(1000),
    "description" text,
    CONSTRAINT "users_email" UNIQUE ("email"),
    CONSTRAINT "users_id" PRIMARY KEY ("id"),
    CONSTRAINT "users_user_name" UNIQUE ("user_name")
) WITH (oids = false);

CREATE INDEX "users_active" ON "users" USING btree ("active");
CREATE INDEX "users_admin" ON "users" USING btree ("admin");
CREATE INDEX "users_full_name" ON "users" USING btree ("full_name");

ALTER TABLE ONLY "public"."users" ALTER COLUMN "id" SET DEFAULT nextval('users_next_id'::regclass);



DROP SEQUENCE IF EXISTS "public"."posts_next_id";
DROP TABLE IF EXISTS "public"."posts";

CREATE SEQUENCE "public"."posts_next_id"
    INCREMENT 1
    START 1
    MINVALUE 1;

CREATE TABLE "public"."posts" (
	"id" integer NOT NULL,
	"path" character varying(200) NOT NULL,
	"title" character varying(200) NOT NULL,
	"created" timestamp(8) with time zone NOT NULL DEFAULT now(),
	"updated" timestamp(8) with time zone NOT NULL DEFAULT now(),
	"perex" text DEFAULT NULL,
	"content" text DEFAULT NULL,
	CONSTRAINT "posts_path" UNIQUE ("path"),
	CONSTRAINT "posts_id" PRIMARY KEY ("id")
);

CREATE INDEX "posts_title" ON "posts" USING btree ("title");
CREATE INDEX "posts_created" ON "posts" USING btree ("created");
CREATE INDEX "posts_updated" ON "posts" USING btree ("updated");

ALTER TABLE ONLY "public"."posts" ALTER COLUMN "id" SET DEFAULT nextval('posts_next_id'::regclass);





DROP SEQUENCE IF EXISTS "public"."comments_next_id";
DROP TABLE IF EXISTS "public"."comments";

CREATE SEQUENCE "public"."comments_next_id"
    INCREMENT 1
    START 1
    MINVALUE 1;

CREATE TABLE "public"."comments" (
	"id" integer NOT NULL,
	"id_post" integer NOT NULL,
	"id_user" integer NOT NULL,
	"created" timestamp(8) with time zone NOT NULL DEFAULT now(),
	"active" smallint NOT NULL DEFAULT 1,
	"title" character varying(200) NOT NULL,
	"content" text DEFAULT NULL,
	CONSTRAINT "comments_id" PRIMARY KEY ("id")
);

CREATE INDEX "comments_active" ON "comments" USING btree ("active");
CREATE INDEX "comments_created" ON "comments" USING btree ("created");
CREATE INDEX "comments_id_user" ON "comments" USING btree ("id_user");
CREATE INDEX "comments_id_post" ON "comments" USING btree ("id_post");
CREATE INDEX "comments_title" ON "comments" USING btree ("title");

ALTER TABLE ONLY "public"."comments" ALTER COLUMN "id" SET DEFAULT nextval('comments_next_id'::regclass);



ALTER TABLE "public"."comments"
	ADD CONSTRAINT "FK_comments_posts" FOREIGN KEY ("id_post") REFERENCES "public"."posts" ("id");
ALTER TABLE "public"."comments"
	ADD CONSTRAINT "FK_comments_users" FOREIGN KEY ("id_user") REFERENCES "public"."users" ("id");



INSERT INTO "users" ("active", "admin", "user_name", "full_name", "email", "password_hash", "avatar_url", "permissions", "roles", "description") VALUES
	(1, 1, 'admin', 'Administrator', 'tomflidr@gmail.com', '$2y$10$s9E56/QH6.a69sJML9aS6enCczRCZcEPrbFh7BYTSrnrn4H9QMF6u', '/Var/Avatars/admin.jpg', NULL, NULL, 'password is: demo'),
	(1, 0, 'johny', 'Johny Depp', 'johny.depp@example.com', '$2y$10$s9E56/QH6.a69sJML9aS6erzn7qVZvc.fqHeOR0nBnWEPLmYMBvs6', 'https://i0.wp.com/see.news/wp-content/uploads/2020/04/Johnny-Depp.jpg', NULL, NULL, 'password is: 1234'),
	(1, 0, 'sandra', 'Sandra Bullock', 'sandra.bullock@example.com', '$2y$10$s9E56/QH6.a69sJML9aS6erzn7qVZvc.fqHeOR0nBnWEPLmYMBvs6', 'https://www.nzherald.co.nz/resizer/ZCUfVPBpXtJtlPCCOLdqmGMPEtE=/360x384/filters:quality(70)/arc-anglerfish-syd-prod-nzme.s3.amazonaws.com/public/MKZFW75SMFARRI3BV727NBHTPI.jpg', NULL, NULL, 'password is: 1234'),
	(1, 1, 'editor', 'Editor', 'editor@example.com', '$2y$10$s9E56/QH6.a69sJML9aS6enCczRCZcEPrbFh7BYTSrnrn4H9QMF6u', 'https://boxesandarrows.com/files/banda/are-your-users-s-t-u/dunce-200.jpg', NULL, NULL, 'password is: demo');

INSERT INTO "posts" ("path", "title", "created", "updated", "perex", "content") VALUES
	('chceme-lidi-zbavit-strachu-z-mdh', 'Chceme lidi zbavit strachu z MHD, říká ředitel pražského dopravního podniku', '2020-05-16 12:00:00', '2020-05-18 12:00:00', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vulputate semper viverra. Curabitur euismod orci vitae erat pellentesque in sagittis nibh porttitor. Curabitur sed augue sapien, a feugiat neque.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vulputate semper viverra. Curabitur euismod orci vitae erat pellentesque in sagittis nibh porttitor. Curabitur sed augue sapien, a feugiat neque. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque bibendum, sapien ac vestibulum tempus, nibh augue aliquet erat, ac tristique felis sapien at ante. In consectetur mattis congue. Nunc eu sapien in nulla hendrerit porttitor non nec eros. Morbi mauris lorem, gravida nec vestibulum bibendum, rutrum vel erat. Ut a pretium turpis. Pellentesque ultrices accumsan volutpat.\r\n\r\nUt ut turpis orci, eget laoreet lorem. Duis neque felis, aliquet ornare fermentum et, ornare eget dui. Ut fermentum arcu nec risus dapibus elementum. Cras posuere auctor fringilla. Etiam auctor felis quis erat malesuada non feugiat est faucibus. Aenean ultricies augue eu erat vulputate quis congue dolor suscipit. Sed commodo ante quis lacus bibendum sit amet imperdiet nulla lobortis. Morbi metus mi, porta eget consectetur at, luctus in urna. Aliquam eu est eu leo mattis facilisis vitae a libero. Suspendisse potenti.'),
	('za-tyden-se-uvolni-rezim-na-hranicich', 'Za týden se uvolní režim na hranicích. Roušky v kancelářích už nejsou povinné', '2020-05-17 12:00:00', '2020-05-18 12:00:00', NULL, 'Mauris egestas ultrices vestibulum. Proin sit amet erat nisi. Aliquam scelerisque malesuada sem quis sollicitudin. Proin id mi felis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Maecenas facilisis laoreet magna sed tincidunt. Donec faucibus pulvinar congue. Integer dictum sodales nisi, non blandit ligula facilisis at. Suspendisse vitae ante vitae mi pharetra egestas nec volutpat turpis. Curabitur tincidunt, libero nec tincidunt ultricies, erat orci dictum quam, sed ullamcorper arcu est ut augue. Ut blandit placerat augue vel sodales. Suspendisse sem nibh, volutpat nec fermentum et, vehicula et nibh. Integer vel justo non libero pulvinar hendrerit. Phasellus sodales magna at lorem adipiscing tempus. Pellentesque ac lacus pretium est feugiat varius. Aenean cursus, magna vel convallis tempor, lorem nunc pellentesque tellus, et tincidunt libero libero ac erat. Maecenas ullamcorper iaculis lorem at vulputate. Sed interdum pharetra tincidunt. Morbi condimentum augue aliquet tellus porttitor at facilisis elit porttitor. Ut sit amet odio est, a pharetra risus.\r\n\r\nCras fermentum ligula sit amet magna consequat id scelerisque lacus placerat. Curabitur ultricies pharetra ligula, a sagittis sem fermentum vitae. Aenean eu libero elit. Nullam non tortor non nisi consequat ornare vitae non ante. Cras a pharetra mauris. Ut interdum semper lorem a laoreet. Ut eget orci at sem molestie cursus eget euismod orci. Pellentesque vel justo nibh, ac euismod quam. Integer non ligula sit amet quam aliquet tempus sollicitudin eget nisl. Duis nec nisl tincidunt purus vestibulum porta. Morbi convallis, lorem at facilisis suscipit, ligula nisi dignissim eros, ut commodo odio justo sit amet metus. Ut eget magna id eros rhoncus commodo sit amet eget nisi. Integer sed metus ac nibh interdum rhoncus nec sit amet lectus.'),
	('ctyricet-procent-cechu-v-karantene-pribralo', 'Čtyřicet procent Čechů v karanténě přibralo, říká Iva Málková', '2020-05-18 12:00:00', '2020-05-18 12:00:00', 'Nullam tempus tempor turpis vel bibendum. Sed rutrum tempus viverra. Suspendisse at mi vel dolor molestie interdum quis vel dui. Cras ut nunc tortor. Donec porttitor aliquam magna eu tincidunt.', 'Nullam tempus tempor turpis vel bibendum. Sed rutrum tempus viverra. Suspendisse at mi vel dolor molestie interdum quis vel dui. Cras ut nunc tortor. Donec porttitor aliquam magna eu tincidunt. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam erat volutpat. Nam rhoncus, nunc ac varius egestas, nibh dolor viverra elit, in sagittis mi augue et ligula. Sed est mi, rutrum at cursus non, interdum vel sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean elementum iaculis purus non malesuada. Suspendisse sit amet felis quam, id posuere magna. Maecenas suscipit, leo vel varius porta, odio felis tincidunt lacus, a tincidunt justo sem vel tellus. Nulla auctor, turpis eget gravida sollicitudin, magna massa accumsan justo, ut condimentum neque dolor quis ligula. Suspendisse potenti. Pellentesque imperdiet rutrum nisi.\r\n\r\nPellentesque ac eros mi, pharetra porttitor felis. Maecenas tempor hendrerit ligula condimentum accumsan. Nam bibendum varius ante non lobortis. Etiam ac nibh hendrerit ligula congue volutpat. Proin non sem ipsum. Nunc aliquam est at enim blandit ut congue urna venenatis. Suspendisse potenti. Nullam a mollis elit. Donec id eros ut erat bibendum molestie laoreet id turpis. Etiam massa neque, sagittis vel feugiat ut, mollis nec nisl. Sed quis feugiat justo. Maecenas aliquam lacus libero, vel rutrum nunc. Vestibulum at risus eu nibh mattis sodales. Phasellus et gravida mi.');

INSERT INTO "comments" ("id_post", "id_user", "created", "active", "title", "content") VALUES
	(1, 1, '2020-05-18 12:00:00', 1, 'Já teda nevim...', 'Hele Johny, řekni z čeho maj ty lidi furt strach???'),
	(1, 2, '2020-05-18 15:12:01', 1, 'Vim ja?', 'Třeba se bojej řidičů v roušách.');
	
