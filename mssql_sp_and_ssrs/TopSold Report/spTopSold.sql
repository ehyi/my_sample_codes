-- ================================================
-- Template generated from Template Explorer using:
-- Create Procedure (New Menu).SQL
--
-- Use the Specify Values for Template Parameters 
-- command (Ctrl-Shift-M) to fill in the parameter 
-- values below.
--
-- This block of comments will not be included in
-- the definition of the procedure.
-- ================================================
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: 1/31/2016
-- Description:	Builds TopSold table daily
-- =============================================
CREATE PROCEDURE spTopSold 
	-- Add the parameters for the stored procedure here

AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

    -- Insert statements for procedure here
	IF OBJECT_ID('dbo.TopSold', 'U') IS NOT NULL
		drop table TopSold;

	SELECT ScanDate, StoreKey, Guid, Item_Name, ScanType
	INTO TopSold
	FROM ViewScanActivity
	WHERE ScanType in (1,3)
	AND Guid NOT IN (SELECT Guid FROM ViewScanActivity WHERE ScanType IN (4,6,10,11));

	CREATE INDEX idxTScanDate ON TopSold (ScanDate);
	CREATE INDEX idxTStoreKey ON TopSold (StoreKey);
	CREATE INDEX idxTGuid ON TopSold (Guid);
	CREATE INDEX idxTItem_Name ON TopSold (Item_Name);
	CREATE INDEX idxTScanType ON TopSold (ScanType);
END
GO
