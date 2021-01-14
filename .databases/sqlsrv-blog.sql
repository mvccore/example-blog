USE [master];
GO

CREATE DATABASE [mvccore_blog] COLLATE SQL_Latin1_General_CP1_CS_AS;
GO

USE [mvccore_blog];
GO

CREATE TABLE [dbo].[comments](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[id_post] [int] NOT NULL,
	[id_user] [int] NOT NULL,
	[created] [datetime] NULL DEFAULT (getdate()),
	[active] [tinyint] NULL DEFAULT 1,
	[title] [nvarchar](200) NOT NULL,
	[content] [ntext]  NULL DEFAULT NULL,
	CONSTRAINT [PK_comments] PRIMARY KEY CLUSTERED (
		[id] ASC
	) WITH (
		PAD_INDEX = OFF,
		STATISTICS_NORECOMPUTE = OFF,
		IGNORE_DUP_KEY = OFF,
		ALLOW_ROW_LOCKS = ON,
		ALLOW_PAGE_LOCKS = ON
	) ON [PRIMARY]
) ON [PRIMARY];

CREATE TABLE [dbo].[posts](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[path] [nvarchar](200) NOT NULL,
	[title] [nvarchar](200) NOT NULL,
	[created] [datetime] NOT NULL DEFAULT (getdate()),
	[updated] [datetime] NOT NULL DEFAULT (getdate()),
	[perex] [ntext] NULL DEFAULT NULL,
	[content] [ntext] NULL DEFAULT NULL,
	CONSTRAINT [PK_posts] PRIMARY KEY CLUSTERED (
		[id] ASC
	) WITH (
		PAD_INDEX = OFF, 
		STATISTICS_NORECOMPUTE = OFF, 
		IGNORE_DUP_KEY = OFF, 
		ALLOW_ROW_LOCKS = ON, 
		ALLOW_PAGE_LOCKS = ON
	) ON [PRIMARY]
) ON [PRIMARY];

CREATE TABLE [dbo].[users](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[active] [tinyint] NOT NULL DEFAULT 1,
	[admin] [tinyint] NOT NULL DEFAULT 0,
	[user_name] [nvarchar](50) NOT NULL,
	[full_name] [nvarchar](100) NOT NULL,
	[email] [nvarchar](200) NOT NULL,
	[password_hash] [varchar](60) NOT NULL,
	[avatar_url] [nvarchar](1000) NULL DEFAULT NULL,
	[permissions] [varchar](1000) NULL DEFAULT NULL,
	[roles] [varchar](1000) NULL DEFAULT NULL,
	[description] [ntext] NULL DEFAULT NULL,
	CONSTRAINT [PK_users] PRIMARY KEY CLUSTERED (
		[id] ASC
	) WITH (
		PAD_INDEX = OFF, 
		STATISTICS_NORECOMPUTE = OFF, 
		IGNORE_DUP_KEY = OFF, 
		ALLOW_ROW_LOCKS = ON, 
		ALLOW_PAGE_LOCKS = ON
	) ON [PRIMARY]
) ON [PRIMARY];


SET IDENTITY_INSERT [dbo].[comments] ON;
INSERT [dbo].[comments] ([id], [id_post], [id_user], [created], [active], [title], [content]) VALUES (2, 1, 1, CAST(N'2020-05-18T12:00:00.000' AS DateTime), 1, N'Já teda nevim...', N'Hele Johny, rekni z ceho maj ty lidi furt strach???')
INSERT [dbo].[comments] ([id], [id_post], [id_user], [created], [active], [title], [content]) VALUES (3, 1, 2, CAST(N'2020-05-18T15:12:01.000' AS DateTime), 1, N'Vim ja?', N'Treba se bojej ridicu v roušách.')
SET IDENTITY_INSERT [dbo].[comments] OFF;

SET IDENTITY_INSERT [dbo].[posts] ON;
INSERT [dbo].[posts] ([id], [path], [title], [created], [updated], [perex], [content]) VALUES (1, N'chceme-lidi-zbavit-strachu-z-mdh', N'Chceme lidi zbavit strachu z MHD, ríká reditel pražského dopravního podniku', CAST(N'2020-05-16T12:00:00.000' AS DateTime), CAST(N'2021-01-13T13:37:58.000' AS DateTime), N'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', N'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vulputate semper viverra. Curabitur euismod orci vitae erat pellentesque in sagittis nibh porttitor. Curabitur sed augue sapien, a feugiat neque. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque bibendum, sapien ac vestibulum tempus, nibh augue aliquet erat, ac tristique felis sapien at ante. In consectetur mattis congue. Nunc eu sapien in nulla hendrerit porttitor non nec eros. Morbi mauris lorem, gravida nec vestibulum bibendum, rutrum vel erat. Ut a pretium turpis. Pellentesque ultrices accumsan volutpat.&#92;r&#92;n&#92;r&#92;nUt ut turpis orci, eget laoreet lorem. Duis neque felis, aliquet ornare fermentum et, ornare eget dui. Ut fermentum arcu nec risus dapibus elementum. Cras posuere auctor fringilla. Etiam auctor felis quis erat malesuada non feugiat est faucibus. Aenean ultricies augue eu erat vulputate quis congue dolor suscipit. Sed commodo ante quis lacus bibendum sit amet imperdiet nulla lobortis. Morbi metus mi, porta eget consectetur at, luctus in urna. Aliquam eu est eu leo mattis facilisis vitae a libero. Suspendisse potenti.')
INSERT [dbo].[posts] ([id], [path], [title], [created], [updated], [perex], [content]) VALUES (2, N'za-tyden-se-uvolni-rezim-na-hranicich', N'Za týden se uvolní režim na hranicích. Roušky v kancelárích už nejsou povinné', CAST(N'2020-05-17T12:00:00.000' AS DateTime), CAST(N'2020-05-18T12:00:00.000' AS DateTime), NULL, N'Mauris egestas ultrices vestibulum. Proin sit amet erat nisi. Aliquam scelerisque malesuada sem quis sollicitudin. Proin id mi felis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Maecenas facilisis laoreet magna sed tincidunt. Donec faucibus pulvinar congue. Integer dictum sodales nisi, non blandit ligula facilisis at. Suspendisse vitae ante vitae mi pharetra egestas nec volutpat turpis. Curabitur tincidunt, libero nec tincidunt ultricies, erat orci dictum quam, sed ullamcorper arcu est ut augue. Ut blandit placerat augue vel sodales. Suspendisse sem nibh, volutpat nec fermentum et, vehicula et nibh. Integer vel justo non libero pulvinar hendrerit. Phasellus sodales magna at lorem adipiscing tempus. Pellentesque ac lacus pretium est feugiat varius. Aenean cursus, magna vel convallis tempor, lorem nunc pellentesque tellus, et tincidunt libero libero ac erat. Maecenas ullamcorper iaculis lorem at vulputate. Sed interdum pharetra tincidunt. Morbi condimentum augue aliquet tellus porttitor at facilisis elit porttitor. Ut sit amet odio est, a pharetra risus.\r\n\r\nCras fermentum ligula sit amet magna consequat id scelerisque lacus placerat. Curabitur ultricies pharetra ligula, a sagittis sem fermentum vitae. Aenean eu libero elit. Nullam non tortor non nisi consequat ornare vitae non ante. Cras a pharetra mauris. Ut interdum semper lorem a laoreet. Ut eget orci at sem molestie cursus eget euismod orci. Pellentesque vel justo nibh, ac euismod quam. Integer non ligula sit amet quam aliquet tempus sollicitudin eget nisl. Duis nec nisl tincidunt purus vestibulum porta. Morbi convallis, lorem at facilisis suscipit, ligula nisi dignissim eros, ut commodo odio justo sit amet metus. Ut eget magna id eros rhoncus commodo sit amet eget nisi. Integer sed metus ac nibh interdum rhoncus nec sit amet lectus.')
INSERT [dbo].[posts] ([id], [path], [title], [created], [updated], [perex], [content]) VALUES (3, N'ctyricet-procent-cechu-v-karantene-pribralo', N'Ctyricet procent Cechu v karanténe pribralo, ríká Iva Málková', CAST(N'2020-05-18T12:00:00.000' AS DateTime), CAST(N'2020-05-18T12:00:00.000' AS DateTime), N'Nullam tempus tempor turpis vel bibendum. Sed rutrum tempus viverra. Suspendisse at mi vel dolor molestie interdum quis vel dui. Cras ut nunc tortor. Donec porttitor aliquam magna eu tincidunt.', N'Nullam tempus tempor turpis vel bibendum. Sed rutrum tempus viverra. Suspendisse at mi vel dolor molestie interdum quis vel dui. Cras ut nunc tortor. Donec porttitor aliquam magna eu tincidunt. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam erat volutpat. Nam rhoncus, nunc ac varius egestas, nibh dolor viverra elit, in sagittis mi augue et ligula. Sed est mi, rutrum at cursus non, interdum vel sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean elementum iaculis purus non malesuada. Suspendisse sit amet felis quam, id posuere magna. Maecenas suscipit, leo vel varius porta, odio felis tincidunt lacus, a tincidunt justo sem vel tellus. Nulla auctor, turpis eget gravida sollicitudin, magna massa accumsan justo, ut condimentum neque dolor quis ligula. Suspendisse potenti. Pellentesque imperdiet rutrum nisi.\r\n\r\nPellentesque ac eros mi, pharetra porttitor felis. Maecenas tempor hendrerit ligula condimentum accumsan. Nam bibendum varius ante non lobortis. Etiam ac nibh hendrerit ligula congue volutpat. Proin non sem ipsum. Nunc aliquam est at enim blandit ut congue urna venenatis. Suspendisse potenti. Nullam a mollis elit. Donec id eros ut erat bibendum molestie laoreet id turpis. Etiam massa neque, sagittis vel feugiat ut, mollis nec nisl. Sed quis feugiat justo. Maecenas aliquam lacus libero, vel rutrum nunc. Vestibulum at risus eu nibh mattis sodales. Phasellus et gravida mi.')
SET IDENTITY_INSERT [dbo].[posts] OFF;

SET IDENTITY_INSERT [dbo].[users] ON;
INSERT [dbo].[users] ([id], [active], [admin], [user_name], [full_name], [email], [password_hash], [avatar_url], [permissions], [roles], [description]) VALUES (1, 1, 1, N'admin', N'Administrator', N'tomflidr@gmail.com', N'$2y$10$s9E56/QH6.a69sJML9aS6enCczRCZcEPrbFh7BYTSrnrn4H9QMF6u', N'/Var/Avatars/admin.jpg', NULL, NULL, N'password is: demo')
INSERT [dbo].[users] ([id], [active], [admin], [user_name], [full_name], [email], [password_hash], [avatar_url], [permissions], [roles], [description]) VALUES (2, 1, 0, N'johny', N'Johny Depp', N'johny.depp@example.com', N'$2y$10$s9E56/QH6.a69sJML9aS6erzn7qVZvc.fqHeOR0nBnWEPLmYMBvs6', N'https://i0.wp.com/see.news/wp-content/uploads/2020/04/Johnny-Depp.jpg', NULL, NULL, N'password is: 1234')
INSERT [dbo].[users] ([id], [active], [admin], [user_name], [full_name], [email], [password_hash], [avatar_url], [permissions], [roles], [description]) VALUES (3, 1, 0, N'sandra', N'Sandra Bullock', N'sandra.bullock@example.com', N'$2y$10$s9E56/QH6.a69sJML9aS6erzn7qVZvc.fqHeOR0nBnWEPLmYMBvs6', N'https://www.nzherald.co.nz/resizer/ZCUfVPBpXtJtlPCCOLdqmGMPEtE=/360x384/filters:quality(70)/arc-anglerfish-syd-prod-nzme.s3.amazonaws.com/public/MKZFW75SMFARRI3BV727NBHTPI.jpg', NULL, NULL, N'password is: 1234')
INSERT [dbo].[users] ([id], [active], [admin], [user_name], [full_name], [email], [password_hash], [avatar_url], [permissions], [roles], [description]) VALUES (4, 1, 1, N'editor', N'Editor', N'editor@example.com', N'$2y$10$s9E56/QH6.a69sJML9aS6enCczRCZcEPrbFh7BYTSrnrn4H9QMF6u', N'https://boxesandarrows.com/files/banda/are-your-users-s-t-u/dunce-200.jpg', NULL, NULL, N'password is: demo')
SET IDENTITY_INSERT [dbo].[users] OFF;


CREATE NONCLUSTERED INDEX [comments_active] ON [dbo].[comments](
	[active] ASC
) WITH (
	PAD_INDEX = OFF,
	STATISTICS_NORECOMPUTE = OFF,
	SORT_IN_TEMPDB = OFF,
	DROP_EXISTING = OFF,
	ONLINE = OFF,
	ALLOW_ROW_LOCKS = ON,
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [comments_created] ON [dbo].[comments](
	[created] ASC
) WITH (
	PAD_INDEX = OFF,
	STATISTICS_NORECOMPUTE = OFF,
	SORT_IN_TEMPDB = OFF,
	DROP_EXISTING = OFF,
	ONLINE = OFF,
	ALLOW_ROW_LOCKS = ON,
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [comments_id_post] ON [dbo].[comments](
	[id_post] ASC
) WITH (
	PAD_INDEX = OFF,
	STATISTICS_NORECOMPUTE = OFF,
	SORT_IN_TEMPDB = OFF,
	DROP_EXISTING = OFF,
	ONLINE = OFF,
	ALLOW_ROW_LOCKS = ON,
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [comments_id_user] ON [dbo].[comments](
	[id_user] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	SORT_IN_TEMPDB = OFF, 
	DROP_EXISTING = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [comments_title] ON [dbo].[comments](
	[title] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	SORT_IN_TEMPDB = OFF, 
	DROP_EXISTING = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];



ALTER TABLE [dbo].[posts] ADD  CONSTRAINT [posts_path] UNIQUE NONCLUSTERED (
	[path] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	IGNORE_DUP_KEY = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [posts_created] ON [dbo].[posts](
	[created] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	SORT_IN_TEMPDB = OFF, 
	DROP_EXISTING = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [posts_title] ON [dbo].[posts](
	[title] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	SORT_IN_TEMPDB = OFF, 
	DROP_EXISTING = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [posts_updated] ON [dbo].[posts](
	[updated] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	SORT_IN_TEMPDB = OFF, 
	DROP_EXISTING = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];



ALTER TABLE [dbo].[users] ADD  CONSTRAINT [users_email] UNIQUE NONCLUSTERED (
	[email] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	IGNORE_DUP_KEY = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [users_active] ON [dbo].[users](
	[active] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	SORT_IN_TEMPDB = OFF, 
	DROP_EXISTING = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [users_admin] ON [dbo].[users](
	[admin] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	SORT_IN_TEMPDB = OFF, 
	DROP_EXISTING = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [users_full_name] ON [dbo].[users](
	[full_name] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	SORT_IN_TEMPDB = OFF, 
	DROP_EXISTING = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];

CREATE NONCLUSTERED INDEX [users_user_name] ON [dbo].[users](
	[user_name] ASC
) WITH (
	PAD_INDEX = OFF, 
	STATISTICS_NORECOMPUTE = OFF, 
	SORT_IN_TEMPDB = OFF, 
	DROP_EXISTING = OFF, 
	ONLINE = OFF, 
	ALLOW_ROW_LOCKS = ON, 
	ALLOW_PAGE_LOCKS = ON
) ON [PRIMARY];



ALTER TABLE [dbo].[comments]  WITH CHECK 
	ADD CONSTRAINT [FK_comments_posts] 
		FOREIGN KEY([id_post])
		REFERENCES [dbo].[posts] ([id]);

ALTER TABLE [dbo].[comments] CHECK CONSTRAINT [FK_comments_posts];



ALTER TABLE [dbo].[comments]  WITH CHECK 
	ADD CONSTRAINT [FK_comments_users] 
		FOREIGN KEY([id_user])
		REFERENCES [dbo].[users] ([id]);

ALTER TABLE [dbo].[comments] CHECK CONSTRAINT [FK_comments_users];