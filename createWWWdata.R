# function to create www-data for DOOR homepage
# 20.11.2008 - updated function to exlude Ors with no data
# 30.07.2009 - code cleanup and rewrite

createWWWdata <- function() {
  require(DoOR.data)
  require(DoOR.functions)
  require(ggplot2)
  loadData()
  
  # get time
  time.a <- Sys.time()
  
  
  # odorant and responding.units names --------------------------------------
  responding.units <- as.character(ORs[, "OR"])
  na.responding.units <-
    colnames(response.matrix)[which(apply(!is.na(response.matrix), 2, sum) == 0)]
  responding.units <-
    responding.units[-which(responding.units %in% na.responding.units)]
  
  odorants            <- odor$InChIKey
  na.odorants <-
    rownames(response.matrix)[which(apply(!is.na(response.matrix), 1, sum) == 0)]
  odorants <- odorants[-which(odorants %in% na.odorants)]
  
  # create folder structure -------------------------------------------------
  
  message("creating folders...")
  oldwd <- getwd()
  dir.create(path = paste("DoOR_www_data_", Sys.Date(), sep = ""))
  setwd(paste(oldwd, "/DoOR_www_data_", Sys.Date(), sep = ""))
  dir.create("data")
  dir.create("data/odorants")
  dir.create("data/responding.units")
  dir.create("data/datasets")
  wd <- getwd()
  setwd(paste0(wd, "/data"))
  
  # reset SFR ---------------------------------------------------------------
  rm.SFRreset <- resetSFR(response.matrix, "SFR")
  
  # general files -----------------------------------------------------------
  message("writing some general files...")
  write.csv(DoOR.mappings, "DoOR.mappings.csv")
  message("......DoOR.mappings.csv.............created")
  write.csv(dataset.info, "dataset.info.csv")   #activate once included
  message("......dataset.info.csv....created")          #activate once included
  write.csv(odor, file = "odorants.csv")
  message("......odorants.csv........created")
  write.csv(cbind(responding.units), "responding.units.csv")
  message("......responding.units.csv.........created")
  #reformat
  exdata <- data.frame(responding.unit = excluded.data$OR)
  for (i in 1:length(excluded.data$OR)) {
    ex <- unlist(strsplit(as.character(excluded.data[i, 2]), ", "))
    if (length(ex) > 0) {
      for (j in 1:length(ex)) {
        exdata[i, j + 1] <- ex[j]
      }
    }
  }
  exdata <- exdata[which(apply(!is.na(exdata[, -1]), 1, sum) > 0), ]
  
  write.csv(exdata, "excluded.data.csv")
  message("......excluded.data.csv.........created")
  
  # odorant data --------------------------------------------
  range <- range(rm.SFRreset, na.rm = T)
  
  pb <- txtProgressBar(min = 0,
                       max = length(odorants),
                       style = 3)
  for (i in 1:length(odorants)) {
    x <- odorants[i]
    write.csv(getNormalizedResponses(x, na.rm = TRUE),
              paste0("odorants/", x, ".csv"))
    setTxtProgressBar(pb, i)
  }
  close(pb)
  
  pb <- txtProgressBar(min = 0,
                       max = length(odorants),
                       style = 3)
  for (i in 1:length(odorants)) {
    x <- odorants[i]
    ggsave(
      plot = dplot_ALmap(
        InChIKey = odorants[i],
        tag = "receptor",
        base_size = 10
      ),
      filename = paste0("odorants/", odorants[i], ".png"),
      width = 12,
      height = 3.8,
      dpi = 100
    )
    setTxtProgressBar(pb, i)
  }
  close(pb)
  
  
  pb <- txtProgressBar(min = 0,
                       max = length(odorants),
                       style = 3)
  for (i in 1:length(odorants)) {
    x <- odorants[i]
    ggsave(
      plot = dplot_tuningCurve(
        odorant = x,
        limits = range,
        base_size = 10,
        fill.odorant = "#4AD058"
      ),
      filename = paste0("odorants/", x, "_tuningCurve.png"),
      width = 3.2,
      height = 3,
      dpi = 100
    )
    setTxtProgressBar(pb, i)
  }
  close(pb)
  
  # responding.units data ---------------------------------------------------
  range <- range(rm.SFRreset, na.rm = T)
  
  pb <-
    txtProgressBar(min = 0,
                   max = length(responding.units),
                   style = 3)
  for (i in 1:length(responding.units)) {
    x <- responding.units[i]
    ggsave(
      plot = dplot_tuningCurve(
        receptor = x,
        limits = range,
        base_size = 10,
        fill.receptor = "#B667DD"
      ),
      filename = paste0("responding.units/", x, "_tuningCurve.png"),
      width = 3,
      height = 3,
      dpi = 100
    )
    setTxtProgressBar(pb, i)
  }
  close(pb)
  
  
  pb <-
    txtProgressBar(min = 0,
                   max = length(responding.units),
                   style = 3)
  for (i in 1:length(responding.units)) {
    x <- responding.units[i]
    profile <-
      data.frame(data.format, model.response = round(rm.SFRreset[, x], 3))
    profile <- profile[-which(is.na(profile$model.response)), ]
    write.csv(profile, paste0("responding.units/", x, ".csv"))
    setTxtProgressBar(pb, i)
  }
  close(pb)
  
  
  
  pb <-
    txtProgressBar(min = 0,
                   max = length(responding.units),
                   style = 3)
  for (i in 1:length(responding.units)) {
    x <- responding.units[i]
    plot <-
      dplot_responseProfile(
        receptor = x,
        tag = "Name",
        limits = range,
        base_size = 10
      )
    n = length(na.omit(rm.SFRreset[, x]))
    ggsave(
      plot = plot,
      filename = paste0("responding.units/", x, "_RP.png"),
      width = 8,
      height = 1 + (n * 0.09),
      dpi = 100
    )
    setTxtProgressBar(pb, i)
  }
  close(pb)
  
  # studies data ------------------------------------------------------------
  datasets <-
    unique(unlist(sapply(load2list(), function(x)
      colnames(x)[-c(1:5)])))
  
  pb <- txtProgressBar(min = 0,
                       max = length(datasets),
                       style = 3)
  for (i in 1:length(datasets)) {
    x <- datasets[i]
    write.csv(getDataset(x, na.rm = TRUE),
              paste0("datasets/dataset_", x, ".csv"))
    setTxtProgressBar(pb, i)
  }
  close(pb)
  
  
  # create a csv which contains the studies that investigated a given receptor:
  ru.studies <- data.frame()
  for (i in 1:length(responding.units)) {
    x <- responding.units[i]
    ru.x <- get(x)
    ru.studies[i, 1] <- x
    k <- 2
    for (j in 6:length(ru.x)) {
      ru.studies[i, k] <- colnames(ru.x)[j]
      k <- k + 1
    }
  }
  write.csv(ru.studies, "datasets/datasets_per_ru.csv")
  
  odorant.datasets <- as.data.frame(odorants)
  
  for (i in 1:length(datasets)) {
    x <- datasets[i]
    matched <-
      match(getDataset(x, na.rm = T)[, "InChIKey"], odorant.datasets[, 1])
    matched <-
      na.omit(matched) # NAs are odorants that were excluded from merging
    odorant.datasets[, i + 1] <- NA
    colnames(odorant.datasets)[i + 1] <- x
    odorant.datasets[matched, i + 1]  <- x
  }
  write.csv(odorant.datasets, "datasets/datasets_per_odorant.csv")
  
  
  ### print time needed and reset working directory ###
  time.b <- Sys.time()
  cat("Finished! Creation of WWW-Data took:",
      round(difftime(time.b, time.a, units = "mins"), 1),
      "minutes\n")
  
  setwd(oldwd)
}