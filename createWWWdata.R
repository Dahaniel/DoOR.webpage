# function to create www-data for DOOR homepage
# 20.11.2008 - updated function to exlude Ors with no data
# 30.07.2009 - code cleanup and rewrite

#createWWWdata <- function() {

  require(DoOR.data)
  require(DoOR.functions)
  require(ggplot2)
  loadData()

  # get time
  time.a <- Sys.time()


# odorant and responding.units names --------------------------------------
  responding.units <- as.character(ORs[, "OR"])
  na.responding.units <- colnames(response.matrix)[which(apply(!is.na(response.matrix),2,sum) == 0)]
  responding.units <- responding.units[-which(responding.units %in% na.responding.units)]

  odorants            <- odor$InChIKey
  na.odorants <- rownames(response.matrix)[which(apply(!is.na(response.matrix),1,sum) == 0)]
  odorants <- odorants[-which(odorants %in% na.odorants)]

# create folder structure -------------------------------------------------

  message("creating folders...")
  oldwd <- getwd()
  dir.create(path=paste("DoOR_www_data_",Sys.Date(), sep=""))
  setwd(paste(oldwd,"/DoOR_www_data_",Sys.Date(), sep=""))
  dir.create("data")
  dir.create("data/odorants")
  dir.create("data/responding.units")
  dir.create("data/datasets")
  wd <- getwd()
  setwd(paste0(wd,"/data"))

# reset SFR ---------------------------------------------------------------
  rm.SFRreset <- resetSFR(response.matrix, "SFR")

# general files -----------------------------------------------------------
  message("writing some general files...")
  write.csv(DoOR.mappings, "DoOR.mappings.csv")
  message("......DoOR.mappings.csv.............created")
  write.csv(dataset.info, "dataset.info.csv")   #activate once included
  message("......dataset.info.csv....created")          #activate once included
  write.csv(odor, file="odorants.csv")
  message("......odorants.csv........created")
  write.csv(cbind(responding.units),"responding.units.csv")
  message("......responding.units.csv.........created")
  #reformat
  exdata <- data.frame(responding.unit = excluded.data$OR)
  for(i in 1:length(excluded.data$OR)) {
    ex <- unlist(strsplit(as.character(excluded.data[i,2]), ", "))
    if(length(ex) > 0) {
      for(j in 1:length(ex)) {
        exdata[i,j+1] <- ex[j]
      }
    }
  }
  exdata <- exdata[which(apply(!is.na(exdata[,-1]),1,sum) > 0),]
  
  write.csv(exdata,"excluded.data.csv")
  message("......excluded.data.csv.........created")

# odorant data --------------------------------------------
  range <- range(rm.SFRreset, na.rm=T)

  pb <- txtProgressBar(min = 0, max = length(odorants), style = 3)
  for(i in 1:length(odorants)) {
    x <- odorants[i]
    write.csv(getNormalizedResponses(x, na.rm = TRUE), paste0("odorants/",x,".csv"))
    setTxtProgressBar(pb, i)
  }
  close(pb)

  pb <- txtProgressBar(min = 0, max = length(odorants), style = 3)
  for(i in 1:length(odorants)) {
    x <- odorants[i]
    ggsave(plot = dplot_ALmap(InChIKey = odorants[i], tag = "receptor", base_size = 10), filename = paste0("odorants/",odorants[i],".png"), width = 12, height = 3.8, dpi = 100)
    setTxtProgressBar(pb, i)
  }
  close(pb)


  pb <- txtProgressBar(min = 0, max = length(odorants), style = 3)
  for(i in 1:length(odorants)) {
    x <- odorants[i]
    ggsave(plot = dplot_tuningCurve(odorant = x, limits = range, base_size = 10, fill.odorant = "#4AD058"), filename = paste0("odorants/",x,"_tuningCurve.png"), width = 3.2, height = 3, dpi = 100)
    setTxtProgressBar(pb, i)
  }
  close(pb)

# responding.units data ---------------------------------------------------
  range <- range(rm.SFRreset, na.rm=T)

  pb <- txtProgressBar(min = 0, max = length(responding.units), style = 3)
  for(i in 1:length(responding.units)) {
    x <- responding.units[i]
    ggsave(plot = dplot_tuningCurve(receptor = x, limits = range, base_size = 10, fill.receptor = "#B667DD"), filename = paste0("responding.units/",x,"_tuningCurve.png"), width = 3, height = 3, dpi = 100)
    setTxtProgressBar(pb, i)
  }
  close(pb)


  pb <- txtProgressBar(min = 0, max = length(responding.units), style = 3)
  for(i in 1:length(responding.units)) {
    x <- responding.units[i]
    profile <- data.frame(data.format, model.response = round(rm.SFRreset[,x], 3))
    profile <- profile[-which(is.na(profile$model.response)),]
    write.csv(profile, paste0("responding.units/",x,".csv"))
    setTxtProgressBar(pb, i)
  }
  close(pb)



  pb <- txtProgressBar(min = 0, max = length(responding.units), style = 3)
  for(i in 1:length(responding.units)) {
    x <- responding.units[i]
    plot <- dplot_responseProfile(receptor = x, tag = "Name", limits = range, base_size = 10)
    n = length(na.omit(rm.SFRreset[,x]))
    ggsave(plot = plot, filename = paste0("responding.units/",x,"_RP.png"), width = 8, height = 1 + (n * 0.09), dpi = 100)
    setTxtProgressBar(pb, i)
  }
  close(pb)

# studies data ------------------------------------------------------------
  datasets <- unique(unlist(sapply(load2list(), function(x) colnames(x)[-c(1:5)])))

  pb <- txtProgressBar(min = 0, max = length(datasets), style = 3)
  for(i in 1:length(datasets)) {
    x <- datasets[i]
    write.csv(getDataset(x, na.rm = TRUE), paste0("datasets/dataset_",x,".csv"))
    setTxtProgressBar(pb, i)
  }
  close(pb)





  # create a csv which contains the studies that investigated a given receptor:
  ru.studies <- data.frame()
  for (i in 1:length(responding.units)) {
    x <- responding.units[i]
    ru.x <- get(x)
    ru.studies[i,1] <- x
    k <- 2
    for (j in 6:length(ru.x)) {
      ru.studies[i,k] <- colnames(ru.x)[j]
      k <- k+1
    }
  }
  write.csv(ru.studies, "datasets/datasets_per_ru.csv")

  odorant.datasets <- as.data.frame(odorants)

  for (i in 1:length(datasets)) {
    x <- datasets[i]
    matched <- match(getDataset(x, na.rm = T)[,"InChIKey"], odorant.datasets[,1])
    matched <- na.omit(matched) # NAs are odorants that were excluded from merging
    odorant.datasets[,i+1] <- NA
    colnames(odorant.datasets)[i+1] <- x
    odorant.datasets[matched, i+1]  <- x
  }
  write.csv(odorant.datasets, "datasets/datasets_per_odorant.csv")




########  
# 
# 
#   odorant.studies <- data.frame()
#   for (i in 1:length(odorants)) {
#     x <- odorants[i]
#     odorant.x <- get(x)
#     ru.studies[i,1] <- x
#     k <- 2
#     for (j in 6:length(ru.x)) {
#       ru.studies[i,k] <- colnames(ru.x)[j]
#       k <- k+1
#     }
#   }
#   write.csv(ru.studies, "datasets/datasets_per_ru.csv")
# 
# 
# 
# 
# 
# 
# 
# 
#   # create csv which contains the studies that investigated a given odorant
#   odorantstudies <- template
#   k<-5
# 
#   for (i in 1:length(studies))
#   {
#     matchedodors <- match(get(paste("study.",studies[i],sep=""))[,4], template[,4])
#     odorantstudies[,k] <- NA
#     colnames(odorantstudies)[k] <- studies[i]
#     odorantstudies[matchedodors,k] <- studies[i]
#     k<-k+1
#   }
#   write.csv(odorantstudies, "studiesByOdors.csv")
#   print("......studiesByOdors.csv....created")
#   print("")
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 
# 

  
  
  
  
#   # tuningCurves ------------------------------------------------------------
# 
# 
#   print("creating tuning breadth plots...")
#   pb <- txtProgressBar(min = 0, max = length(ORNames), style = 3)
#   for (i in 1:length(ORNames)) {
#     png(file=paste(ORNames[i],"TB","png", sep="."), width=300, height=300, pointsize=12,  antialias="default", bg="white")
#     minylim <- min(response.matrix.SFRreset[,ORNames[i]],na.rm=T)
#     if (minylim > 0) minylim <- 0
#     tuningBreadth(na.omit(response.matrix.SFRreset[-1,ORNames[i]]),ylim=c(minylim,1),main=ORNames[i],xlab="odorants",ylab="normalized response")
#     dev.off()
#     #print(paste("TB.",ORNames[i],".png..........created, ", sep=""))
#     setTxtProgressBar(pb, i)
#   }
#   close(pb)
#   print("")
# 
# # response profile --------------------------------------------------------
# 
# 
#   print("exporting response profile as barplot & csv...")
#   pb <- txtProgressBar(min = 0, max = length(ORNames), style = 3)
# 
#   k <- length(ORNames)-1
#   for (i in 1:length(ORNames)) {
# 
#     matched <- match(get(ORNames[i])$CAS, rownames(response.matrix))
#     ORx <- cbind(get(ORNames[i])[,1:4],"modeled.response" = round(response.matrix[matched,ORNames[i]],2))
#     # next two lines are the same but with resetting to SFR first
#     matched.SFRreset <- match(get(ORNames[i])$CAS, rownames(response.matrix.SFRreset))
#     ORx.SFRreset <- cbind(get(ORNames[i])[,1:4],"modeled.response" = round(response.matrix.SFRreset[matched,ORNames[i]],2))
# 
#     #write.csv(ORx, file=paste(ORNames[i],"RP","csv", sep=".")) # SFR not resetted
# 
#     # order by name
#     ORx.SFRreset <- ORx.SFRreset[order(ORx.SFRreset$Name),]
#     write.csv(ORx.SFRreset, file=paste(ORNames[i],"RP","csv", sep=".")) # SFR resetted
# 
#     #	 if(is.na(ORx[ORx$CAS=='SFR',][,5])) {
#     #	  PlotChemicalsDaniel(ORx[order(ORx[,5],decreasing=TRUE),], JPEG=T, point.size=22, file=paste(ORNames[i],"RP","jpg", sep="."), notation="Name", col.extrem=c("blue","red"))
#     #	 } else {
#     #	   PlotChemicalsDaniel(ORx[order(ORx[,5],decreasing=TRUE),], JPEG=T, point.size=22, zero="SFR", file=paste(ORNames[i],"RP","jpg", sep="."), notation="Name", col.extrem=c("blue","red"))
#     #	   }
# 
#     PlotChemicalsDaniel(ORx.SFRreset[order(ORx.SFRreset[,5],decreasing=TRUE),], JPEG=T, point.size=22, file=paste(ORNames[i],"RP","jpg", sep="."), notation="Name", col.extrem=c("blue","red"))
# 
#     k <- k-1
#     setTxtProgressBar(pb, i)
#   }
#   close(pb)
#   print("")
# 
# 
# 
# 
# 
# 
# # studywise ---------------------------------------------------------------
#   setwd("studies")
# 
# 
#   ### create list of studies                                                                    ###
#   ###  shorter version from Shouwen: studies <- names(response.range)                           ###
#   ###  switched back to this version as it is more secure, using only studies where data exists ###
#   print("exporting list of studies...")
#   pb <- txtProgressBar(min = 0, max = length(ORNames), style = 3)
#   studies <- c()
#   for (i in 1:length(ORNames))
#   {
#     ORx <- get(ORNames[i])
#     #studies <- levels(as.factor(c(studies,colnames(ORx)[-4])))
#     studies <- levels(as.factor(c(studies,colnames(ORx[-(1:4)]))))
#     setTxtProgressBar(pb, i)
#   }
#   close(pb)
#   print("")
# 
#   ### search for ORs which were tested by study ###
# 
#   ### find longest odorant list ###
#   print("resorting data studywise...")
#   pb <- txtProgressBar(min = 0, max = length(studies), style = 3)
#   template <- data.frame(odor[,c(1:2,5,4)])
# 
#   for (i in 1:length(studies))
#   {
#     studyx <- template
# 
#     for (j in 1:length(ORNames))
#     {
#       ORx <- get(ORNames[j])
#       if (length(which(colnames(ORx) == studies[i])) > 0)
#       {
#         col <- which(colnames(ORx) == studies[i]) #column which contains study
#         studyx.col <- data.frame("CAS"=ORx[,"CAS"],round(ORx[,col],2)) #copy column, keep CAS and round response
#         match.CAS <- match(template[,"CAS"],studyx.col[,"CAS"])    #match by CAS
#         studyx <- cbind(studyx,studyx.col[match.CAS,2])	#
#         colnames(studyx)[length(studyx)] <- ORNames[j]		# assign OR as colname
#       }
#     }
# 
#     # delete lines/odors which contain only NAs
#     todelete<-numeric()
#     for (k in 1:dim(studyx)[1])
#     {
#       if (all(is.na(studyx[k,-(1:4)])) == T)
#       {
#         todelete<-c(todelete,k)
#       }
#     }
#     studyx <- studyx[-todelete,]
# 
#     # assign studyname
#     assign(paste("study.",studies[i],sep=""), studyx) # opposite would be get()
#     setTxtProgressBar(pb, i)
#   }
#   close(pb)
#   print("")
#   #get ORs tested
# 
#   maxORs <- numeric()
#   for (i in 1:length(studies)) maxORs <- c(maxORs, length(get(paste("study.",studies[i],sep="")))-4)
#   maxORs <- which.max(maxORs)
# 
#   receptors <- character()
#   for (i in 1:length(studies))
#   {
#     study <- c(studies[i],colnames(get(paste("study.",studies[i],sep="")))[5:length(get(paste("study.",studies[i],sep="")))])
#     linestoadd <- (length(get(paste("study.",studies[maxORs],sep="")))-4) - (length(study)-1) 	# how many lines to fill
#     study <- (append(study, rep("",linestoadd)))
#     receptors <- rbind(receptors, study)
#   }
# 
#   # write all the resorted data
#   # write.csv(studies, "study.list.csv")  #replaced by manual edited "study.info.csv"
#   print("writing resorted data...")
#   pb <- txtProgressBar(min = 0, max = length(studies), style = 3)
#   write.csv(receptors, "study.receptors.csv")
#   for(i in 1:length(studies))
#   {
#     write.csv(get(paste("study.",studies[i],sep="")), paste("study.",studies[i],".csv",sep=""))
#     setTxtProgressBar(pb, i)
#   }
#   close(pb)
#   print("")
# 
#   # create a csv which contains the studies that investigated a given receptor:
#   print("create some tables regarding the studies...")
#   OR.studies <- data.frame()
#   for (i in 1:length(ORNames))
#   {
#     ORx <- get(ORNames[i])
#     OR.studies[i,1] <- ORNames[i]
#     k <- 2
#     for (j in 5:length(ORx)) {
#       OR.studies[i,k] <- colnames(ORx)[j]
#       k<-k+1
#     }
#   }
#   write.csv(OR.studies, "studiesByORs.csv")
#   print("......studies.csv...........created")
# 
#   # create csv which contains the studies that investigated a given odorant
#   odorantstudies <- template
#   k<-5
# 
#   for (i in 1:length(studies))
#   {
#     matchedodors <- match(get(paste("study.",studies[i],sep=""))[,4], template[,4])
#     odorantstudies[,k] <- NA
#     colnames(odorantstudies)[k] <- studies[i]
#     odorantstudies[matchedodors,k] <- studies[i]
#     k<-k+1
#   }
#   write.csv(odorantstudies, "studiesByOdors.csv")
#   print("......studiesByOdors.csv....created")
#   print("")
# 
# 
#   ### change wd ###
# 
#   setwd("../odorants")
# 
# 
#   ### get odorants and remove those which only contain NAs (why do we have those?) ###
#   ### and transpose response.matrix.SFRreset                                       ###
# 
#   response.matrix.SFRreset2 <- t(response.matrix.SFRreset)		# transpose response.matrix to be able to use tuningBreadth
#   odorants.naomit <- names(which(apply((is.na(response.matrix.SFRreset2)==F),2,sum) != 0))
# 
# 
# 
# # AL plots ----------------------------------------------------------------
# 
# 
# 
# 
#   #    ### old version using findRespNorm
# 
#   #	    k <- dim(odor)[1]*2-1       # just a counter to display odorants left
#   #	    for (i in 1:dim(odor)[1])
#   #	    {
#   #		    odorX <- (odor[i,4])
#   #		    RPodorX <- findRespNorm(odorX,zero="SFR", responseMatrix=response.matrix)
#   #		    write.csv(RPodorX, file=paste(odorX,"csv", sep="."))
#   #		    print(paste(odorX,".csv..........created, ",k," files to do.", sep=""))
# 
#   #		    k <- k-1                  # just a counter to display odorants left
# 
#   #		    png(file=paste(odorX,"png", sep="."), width=800, height=280, pointsize=14,  antialias="default", bg="#ededed")
#   #		    ALimage(response.data=RPodorX, main=odor[i,2], tag.ALimage="Ors", OGN=OGN, AL256=AL256)
#   #		    dev.off()
#   #		    print(paste(odorX,".png..........created, ",k," files to do.", sep=""))
# 
#   #		    k <- k-1                  # just a counter to display odorants left
#   #	    }
# 
#   ### new version using previously reset response.matrix.SFRreset2
# 
#   #k <- length(odorants.naomit)*2-1       # just a counter to display odorants left
#   print("creating AL plots...")
#   pb <- txtProgressBar(min = 0, max = length(odorants.naomit), style = 3)
#   for (i in 1:length(odorants.naomit))
#   {
#     odorX <- (odorants.naomit[i])
#     odorX.values <- round(response.matrix.SFRreset2[,odorX],2)
#     odorX.receptors <- names(response.matrix.SFRreset2[,odorX])
# 
#     RPodorX <- data.frame("ORs"=odorX.receptors, "Odor"=odorX, "Response"=odorX.values)
#     write.csv(RPodorX, file=paste(odorX,"csv", sep="."))
#     #print(paste(odorX,".csv..........created, ",k," files to do.", sep=""))
# 
#     #k <- k-1                  # just a counter to display odorants left
# 
#     png(file=paste(odorX,"png", sep="."), width=800, height=280, pointsize=14,  antialias="default", bg="#ededed")
#     ALimage(response.data=RPodorX, main=odorants.naomit[i], tag.ALimage="Ors", OGN=OGN, AL256=AL256)
#     dev.off()
#     #print(paste(odorX,".png..........created, ",k," files to do.", sep=""))
# 
#     #k <- k-1                  # just a counter to display odorants left
#     setTxtProgressBar(pb, i)
#   }
#   close(pb)
#   print("")
# 
# 
#   ### print time needed and reset working directory ###
#   time.b <- Sys.time()
#   cat("Finished! Creation of WWW-Data took:", round(difftime(time.b,time.a,units="mins"),1),"minutes\n")
# 
#   setwd(oldwd)
# 
# 
# 
# #}
# 
# 
# 



