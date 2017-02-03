USE [EbisuReport]
GO

/****** Object:  StoredProcedure [dbo].[sp_rptScanStatisticsSales]    Script Date: 06/04/2014 18:44:24 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO




CREATE procedure [dbo].[sp_rptScanStatisticsSales] (
	 @StartDate datetime 
	,@EndDate	datetime
	,@StoreKey	int
	,@RptOrder  int = null
	)
as 
begin 
	SET NOCOUNT ON;

--declare @RptOrder int = 0

	declare @message		varchar(255)
	declare @rc				int
	declare @ProcedureName	sysname
	declare @Error			int

	declare @sRptDate		date 
	declare @eRptDate		date 

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

	select
	Item_ID,
	Item_Name,
	count(distinct guid) as commissioned
	into #a1
	from #tempActivity
	group by Item_ID, Item_name

	select
	Item_ID,
	Item_Name,
	count(distinct guid) as expired
	into #a2
	from #tempActivity
	where ScanType in (4,6,11)
	group by Item_ID, Item_name

	if @RptOrder is null begin
		select top 5
		a1.item_name,
		a1.commissioned,
		a2.expired,
		a1.commissioned - a2.expired as sales
		from #a1 as a1
		inner join #a2 as a2 on a1.item_id = a2.item_id
		order by sales desc, commissioned desc
	end
	if @RptOrder is not null begin
		select top 5
		a1.item_name,
		a1.commissioned,
		a2.expired,
		a1.commissioned - a2.expired as sales
		from #a1 as a1
		inner join #a2 as a2 on a1.item_id = a2.item_id
		order by sales asc, commissioned asc
	end

	drop table #tempActivity
	drop table #a1
	drop table #a2
	
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


