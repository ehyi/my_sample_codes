USE [EbisuReport]
GO

/****** Object:  StoredProcedure [dbo].[sp_rptScanStatistics]    Script Date: 06/04/2014 18:43:43 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO



create procedure [dbo].[sp_rptScanStatistics] (
	 @StartDate datetime 
	,@EndDate	datetime
	,@StoreKey	int)
as 
begin 
	declare @message		varchar(255)
	declare @rc				int
	declare @ProcedureName	sysname
	declare @Error			int

	declare @sRptDate		date 
	declare @eRptDate		date 
	
	declare @total				int
	declare @commissioned		int
	declare @notcommissioned	int
	declare @totalbelt			int
	declare @expired			int
	declare @expnotdiscarded	int
	declare @discarded			int
	declare @discardednotexp	int
	declare @wasted				int
	declare @sold				int

	/**Set Date ranges based on ReportType**/
	select @sRptDate = cast(@StartDate as date)
	select @eRptDate = cast(@EndDate as date)

	select *
	into #tempActivity
	from ViewScanActivity
	where (ScanDate >= @sRptDate and ScanDate <= @eRptDate)	
	and StoreKey = @StoreKey

	create index idx_guid on #tempActivity (guid)
	create index idx_scantype on #tempActivity (scantype)

	-- total
	select @total = COUNT(distinct guid) 
	from #tempActivity

	-- commissioned
	select @commissioned = COUNT(guid) 
	from #tempActivity
	where ScanType=1 

	-- not commissioned
	select @notcommissioned	 = COUNT(distinct guid)
	from #tempActivity a1
	--where (select COUNT(distinct (item_id)) from tempActivity a2 where a1.guid = a2.guid) = 1
	where guid not in (select guid from #tempActivity where item_id <> 0)
	and item_id = 0

	-- total on belt
	select @totalbelt = COUNT(distinct guid) 
	from #tempActivity
	where ScanType in (1,3)

	-- expired
	select @expired = COUNT(distinct guid) 
	from #tempActivity
	where ScanType in (4,6,11)

	-- expired but not discarded
	select @expnotdiscarded	= count(distinct guid)
	from #tempActivity
	where ScanType in (4,6)
	and guid not in (select guid from #tempActivity where ScanType in (11))

	-- discarded
	select @discarded = COUNT(distinct guid) 
	from #tempActivity
	where ScanType in (10,11)

	-- discarded but not expired
	select @discardednotexp = count(distinct guid)
	from #tempActivity
	where ScanType in (10)
	and guid not in (select guid from #tempActivity where ScanType in (11))

	-- wasted
	select @wasted = count(distinct guid)
	from #tempActivity
	where ScanType in (4,6,10,11)

	-- sold
	select @sold = COUNT(distinct guid) 
	from #tempActivity
	where ScanType in (1,3)
	and guid not in (select guid from #tempActivity where ScanType in (4,6,10,11))

	select
		@total as total,
		@commissioned as commissioned,
		@notcommissioned as notcommissioned,
		@totalbelt as totalbelt,
		@expired as expired,
		@expnotdiscarded as expnotdiscarded,
		@discarded as discarded,
		@discardednotexp as discardednotexp,
		@wasted as wasted,
		@sold as sold


	drop table #tempActivity
	
	select @Error = @@Error
	if @Error <> 0 goto ErrorHandler

	set @rc = 0 
	goto ExitProc

ErrorHandler:
	set @rc = 1
	raiserror ('Error in %s: %s', 16, 1, @ProcedureName, @message)

ExitProc:
	return @rc
	
end


GO


