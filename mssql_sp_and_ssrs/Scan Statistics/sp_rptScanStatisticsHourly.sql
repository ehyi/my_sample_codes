USE [EbisuReport]
GO

/****** Object:  StoredProcedure [dbo].[sp_rptScanStatisticsHourly]    Script Date: 06/04/2014 18:44:05 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO




CREATE procedure [dbo].[sp_rptScanStatisticsHourly] (
	 @StartDate datetime 
	,@EndDate	datetime
	,@StoreKey	int)
as 
begin 
	set nocount on

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
	s.Guid,
	datepart("hh", s.FirstScan) as hour,
	s.Workstation_Name,
	s.ScanType,
	1 as commissioned,
	case when s2.Guid is null then 0 else 1 end as expired,
	case when s3.Guid is null then 0 else 1 end as discarded,
	0 as notcommissioned
	from #tempActivity s
	left outer join (select distinct Guid from #tempActivity where ScanType in (4,6,11)) s2 on s.Guid = s2.Guid
	left outer join (select distinct Guid from #tempActivity where ScanType in (10,11))  s3 on s.Guid = s3.Guid
	where s.ScanType in (1)
	--and s1.workstation_id=1
	
	union
	
	select
	s.Guid,
	datepart("hh", min(s.FirstScan)) as hour,
	null as Workstation_Name,
	null as ScanType,
	0 as commissioned,
	case when min(s2.Guid) is null then 0 else 1 end as expired,
	case when min(s3.Guid) is null then 0 else 1 end as discarded,
	1 as notcommissioned
	from #tempActivity s
	left outer join (select distinct Guid from #tempActivity where ScanType in (4,6,11)) s2 on s.Guid = s2.Guid
	left outer join (select distinct Guid from #tempActivity where ScanType in (10,11))  s3 on s.Guid = s3.Guid
	where ScanType not in (1)
	and s.Guid not in (
		select distinct guid
		from #tempActivity
		where ScanType in (1)
	)
	group by s.Guid
	
	order by hour
	

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


